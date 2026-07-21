<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\Supplier;
use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminInventoryController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory', [
            'products' => Product::query()
                ->with('category')
                ->orderBy('name')
                ->get(),
            'suppliers' => Supplier::query()
                ->orderBy('name')
                ->get(),
            'movements' => StockHistory::query()
                ->with(['product.category', 'supplier', 'user'])
                ->latest('created_at')
                ->limit(20)
                ->get(),
            'statistics' => [
                'supplier' => Supplier::count(),
                'movement_total' => StockHistory::count(),
                'movement_in' => StockHistory::where('type', 'purchase')->count(),
                'movement_opname' => StockHistory::where('type', 'opname')->count(),
                'movement_damage' => StockHistory::whereIn('type', ['damaged', 'lost'])->count(),
            ],
        ]);
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:150', 'unique:suppliers,name'],
            'supplier_phone' => ['nullable', 'string', 'max:30'],
            'supplier_email' => ['nullable', 'email', 'max:150'],
            'supplier_address' => ['nullable', 'string', 'max:1000'],
            'supplier_is_active' => ['nullable', 'boolean'],
        ]);

        Supplier::create([
            'name' => $validated['supplier_name'],
            'phone' => $validated['supplier_phone'] ?? null,
            'email' => $validated['supplier_email'] ?? null,
            'address' => $validated['supplier_address'] ?? null,
            'is_active' => $request->boolean('supplier_is_active', true),
        ]);

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function storePurchase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_product_id' => ['required', 'integer', 'exists:products,id'],
            'purchase_supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'purchase_quantity' => ['required', 'integer', 'min:1'],
            'purchase_unit_cost' => ['required', 'numeric', 'min:0'],
            'purchase_reference_number' => ['nullable', 'string', 'max:100'],
            'purchase_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['purchase_product_id']);
            $beforeStock = $product->stock;
            $quantity = (int) $validated['purchase_quantity'];
            $afterStock = $beforeStock + $quantity;
            $unitCost = (float) $validated['purchase_unit_cost'];

            $product->update([
                'stock' => $afterStock,
                'cost_price' => $unitCost,
            ]);

            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'supplier_id' => $validated['purchase_supplier_id'] ?? null,
                'type' => 'purchase',
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'reference_number' => $validated['purchase_reference_number'] ?? null,
                'notes' => $validated['purchase_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Stok masuk/pembelian berhasil disimpan.');
    }

    public function storeOpname(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opname_product_id' => ['required', 'integer', 'exists:products,id'],
            'opname_counted_stock' => ['required', 'integer', 'min:0'],
            'opname_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['opname_product_id']);
            $beforeStock = $product->stock;
            $afterStock = (int) $validated['opname_counted_stock'];
            $adjustment = abs($afterStock - $beforeStock);

            $product->update([
                'stock' => $afterStock,
            ]);

            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'type' => 'opname',
                'quantity' => $adjustment,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_number' => 'OP-' . now()->format('YmdHis'),
                'notes' => $validated['opname_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Stok opname berhasil disimpan.');
    }

    public function storeDamage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'damage_product_id' => ['required', 'integer', 'exists:products,id'],
            'damage_type' => ['required', Rule::in(['damaged', 'lost'])],
            'damage_quantity' => ['required', 'integer', 'min:1'],
            'damage_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['damage_product_id']);
            $beforeStock = $product->stock;
            $quantity = (int) $validated['damage_quantity'];

            if ($quantity > $beforeStock) {
                throw ValidationException::withMessages([
                    'damage_quantity' => 'Jumlah rusak/hilang tidak boleh melebihi stok tersedia.',
                ]);
            }

            $afterStock = $beforeStock - $quantity;
            $product->update([
                'stock' => $afterStock,
            ]);

            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'type' => $validated['damage_type'],
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_number' => strtoupper($validated['damage_type']) . '-' . now()->format('YmdHis'),
                'notes' => $validated['damage_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Data rusak/hilang berhasil disimpan.');
    }
}

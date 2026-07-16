<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\StockMovement;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Product\Models\Product;
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
            'movements' => StockMovement::query()
                ->with(['product.category', 'supplier', 'user'])
                ->latest('created_at')
                ->limit(20)
                ->get(),
            'statistics' => [
                'supplier' => Supplier::count(),
                'movement_total' => StockMovement::count(),
                'movement_in' => StockMovement::where('type', 'purchase')->count(),
                'movement_opname' => StockMovement::where('type', 'opname')->count(),
                'movement_damage' => StockMovement::whereIn('type', ['damaged', 'lost'])->count(),
            ],
        ]);
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:suppliers,name'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Supplier::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function storePurchase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);
            $beforeStock = $product->stock;
            $quantity = (int) $validated['quantity'];
            $afterStock = $beforeStock + $quantity;

            $product->update([
                'stock' => $afterStock,
                'cost_price' => $validated['unit_cost'],
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'user_id' => $request->user()?->id,
                'type' => 'purchase',
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $quantity * (float) $validated['unit_cost'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Stok masuk/pembelian berhasil disimpan.');
    }

    public function storeOpname(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'counted_stock' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);
            $beforeStock = $product->stock;
            $afterStock = (int) $validated['counted_stock'];
            $adjustment = abs($afterStock - $beforeStock);

            $product->update([
                'stock' => $afterStock,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'type' => 'opname',
                'quantity' => $adjustment,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_number' => 'OP-' . now()->format('YmdHis'),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Stok opname berhasil disimpan.');
    }

    public function storeDamage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', Rule::in(['damaged', 'lost'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);
            $beforeStock = $product->stock;
            $quantity = (int) $validated['quantity'];

            if ($quantity > $beforeStock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah rusak/hilang tidak boleh melebihi stok tersedia.',
                ]);
            }

            $afterStock = $beforeStock - $quantity;
            $product->update([
                'stock' => $afterStock,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_number' => strtoupper($validated['type']) . '-' . now()->format('YmdHis'),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Data rusak/hilang berhasil disimpan.');
    }
}

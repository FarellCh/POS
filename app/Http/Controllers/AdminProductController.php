<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Category;
use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function edit(Product $product): View
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', [
            'product' => $product->load('category'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock_adjustment' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $request, $product): void {
            $product->update([
                'category_id' => $validated['category_id'] ?? null,
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            $stockAdjustment = (int) ($validated['stock_adjustment'] ?? 0);

            if ($stockAdjustment > 0) {
                $beforeStock = $product->stock;
                $product->increment('stock', $stockAdjustment);
                $afterStock = $beforeStock + $stockAdjustment;

                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'type' => 'in',
                    'quantity' => $stockAdjustment,
                    'before_stock' => $beforeStock,
                    'after_stock' => $afterStock,
                    'reference' => 'Admin stock adjustment',
                    'reference_number' => 'ADJ-' . now()->format('YmdHis'),
                ]);
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($request->integer('product_id') ?: null),
            ],
            'name' => ['required', 'string', 'max:150'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $stockInput = (int) $validated['stock'];

            if (!empty($validated['product_id'])) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($validated['product_id']);

                $beforeStock = $product->stock;
                $afterStock = $beforeStock + $stockInput;

                $product->update([
                    'category_id' => $validated['category_id'] ?? null,
                    'sku' => $validated['sku'],
                    'name' => $validated['name'],
                    'cost_price' => $validated['cost_price'],
                    'selling_price' => $validated['selling_price'],
                    'stock' => $afterStock,
                    'is_active' => $request->boolean('is_active', true),
                ]);

                if ($stockInput > 0) {
                    StockHistory::create([
                        'product_id' => $product->id,
                        'user_id' => $request->user()?->id,
                        'type' => 'in',
                        'quantity' => $stockInput,
                        'before_stock' => $beforeStock,
                        'after_stock' => $afterStock,
                        'reference' => 'Admin stock addition',
                        'reference_number' => 'ADD-' . now()->format('YmdHis'),
                    ]);
                }

                return;
            }

            $product = Product::create([
                'category_id' => $validated['category_id'] ?? null,
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $stockInput,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($stockInput > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'type' => 'in',
                    'quantity' => $stockInput,
                    'before_stock' => 0,
                    'after_stock' => $stockInput,
                    'reference' => 'Initial stock',
                    'reference_number' => 'NEW-' . now()->format('YmdHis'),
                ]);
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', !empty($validated['product_id']) ? 'Stok barang berhasil ditambahkan.' : 'Barang berhasil ditambahkan.');
    }
}

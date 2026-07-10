<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
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

                $product->update([
                    'category_id' => $validated['category_id'] ?? null,
                    'sku' => $validated['sku'],
                    'name' => $validated['name'],
                    'cost_price' => $validated['cost_price'],
                    'selling_price' => $validated['selling_price'],
                    'stock' => $product->stock + $stockInput,
                    'is_active' => $request->boolean('is_active', true),
                ]);

                if ($stockInput > 0) {
                    StockHistory::create([
                        'product_id' => $product->id,
                        'user_id' => $request->user()?->id,
                        'type' => 'in',
                        'quantity' => $stockInput,
                        'reference' => 'Admin stock addition',
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
                    'reference' => 'Initial stock',
                ]);
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', !empty($validated['product_id']) ? 'Stok barang berhasil ditambahkan.' : 'Barang berhasil ditambahkan.');
    }
}

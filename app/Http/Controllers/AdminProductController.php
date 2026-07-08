<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $product = Product::create([
                'category_id' => $validated['category_id'] ?? null,
                'sku' => $validated['sku'],
                'name' => $validated['name'],
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ((int) $validated['stock'] > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'type' => 'in',
                    'quantity' => (int) $validated['stock'],
                    'reference' => 'Initial stock',
                ]);
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Barang berhasil ditambahkan.');
    }
}

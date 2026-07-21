<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $result = DB::transaction(function () use ($validated, $request) {
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_id']);

            if ($validated['quantity'] > $product->stock) {
                return null;
            }

            $beforeStock = $product->stock;
            $remainingStock = $product->stock - $validated['quantity'];
            $product->stock = $remainingStock;
            $product->save();

            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'type' => 'out',
                'quantity' => $validated['quantity'],
                'before_stock' => $beforeStock,
                'after_stock' => $remainingStock,
                'reference' => 'Kasir confirm',
                'reference_number' => 'SALE-' . now()->format('YmdHis'),
            ]);

            return [
                'product_id' => $product->id,
                'remaining_stock' => $remainingStock,
            ];
        });

        if ($result === null) {
            return response()->json([
                'message' => 'Stok tidak mencukupi.',
            ], 422);
        }

        return response()->json($result);
    }
}

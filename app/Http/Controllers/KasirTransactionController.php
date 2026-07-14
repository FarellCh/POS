<?php

namespace App\Http\Controllers;

use App\Domains\Payment\Models\PaymentMethod;
use App\Domains\Product\Models\Product;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasirTransactionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:50', 'unique:transactions,invoice_number'],
            'payment_method' => ['required', 'string', 'max:50'],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $existingTransaction = Transaction::query()
            ->with('details')
            ->where('invoice_number', $validated['invoice_number'])
            ->first();

        if ($existingTransaction) {
            return response()->json([
                'transaction_id' => $existingTransaction->id,
                'invoice_number' => $existingTransaction->invoice_number,
                'grand_total' => (float) $existingTransaction->grand_total,
                'payment_method' => $existingTransaction->payment_method,
            ]);
        }

        $transaction = DB::transaction(function () use ($validated, $request) {
            $paymentMethod = PaymentMethod::query()
                ->active()
                ->where('code', $validated['payment_method'])
                ->first();

            if (! $paymentMethod) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Metode pembayaran tidak tersedia.',
                ]);
            }

            $productIds = collect($validated['cart'])->pluck('product_id')->all();
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'cart' => 'Salah satu produk tidak ditemukan.',
                ]);
            }

            $totalCost = 0.0;
            $totalAmount = 0.0;
            $details = [];

            foreach ($validated['cart'] as $item) {
                $product = $products->get($item['product_id']);
                $quantity = (int) $item['quantity'];

                if (! $product) {
                    throw ValidationException::withMessages([
                        'cart' => 'Produk pada keranjang tidak valid.',
                    ]);
                }

                $costPrice = (float) $product->cost_price;
                $sellingPrice = (float) $product->selling_price;
                $subtotalCost = $costPrice * $quantity;
                $subtotalAmount = $sellingPrice * $quantity;

                $totalCost += $subtotalCost;
                $totalAmount += $subtotalAmount;

                $details[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'cost_price_at_transaction' => $costPrice,
                    'selling_price_at_transaction' => $sellingPrice,
                    'subtotal' => $subtotalAmount,
                ];
            }

            $grandTotal = $totalAmount;

            $transaction = Transaction::create([
                'invoice_number' => $validated['invoice_number'],
                'user_id' => $request->user()?->id,
                'total_cost' => $totalCost,
                'total_amount' => $totalAmount,
                'discount_amount' => 0,
                'grand_total' => $grandTotal,
                'paid_amount' => $grandTotal,
                'change_amount' => 0,
                'payment_method' => $paymentMethod->label,
            ]);

            foreach ($details as $detailData) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    ...$detailData,
                ]);
            }

            return $transaction->load('details');
        });

        return response()->json([
            'transaction_id' => $transaction->id,
            'invoice_number' => $transaction->invoice_number,
            'grand_total' => (float) $transaction->grand_total,
            'payment_method' => $transaction->payment_method,
        ]);
    }
}

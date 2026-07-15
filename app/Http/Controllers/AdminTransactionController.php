<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use App\Domains\Transaction\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminTransactionController extends Controller
{
    public function void(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($request->user()?->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'void_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($transaction, $request, $validated): void {
            $lockedTransaction = Transaction::query()
                ->with('details')
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            if ($lockedTransaction->is_voided) {
                throw ValidationException::withMessages([
                    'void_reason' => 'Transaksi ini sudah di-void.',
                ]);
            }

            $productIds = $lockedTransaction->details->pluck('product_id')->filter()->unique()->values()->all();
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($lockedTransaction->details as $detail) {
                $product = $detail->product_id ? $products->get($detail->product_id) : null;

                if (! $product) {
                    continue;
                }

                $restoredStock = $product->stock + (int) $detail->quantity;
                $product->stock = $restoredStock;
                $product->save();

                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'type' => 'in',
                    'quantity' => (int) $detail->quantity,
                    'reference' => 'Void transaction #' . $lockedTransaction->invoice_number,
                ]);
            }

            $lockedTransaction->update([
                'is_voided' => true,
                'voided_at' => now(),
                'voided_by' => $request->user()?->id,
                'void_reason' => $validated['void_reason'],
            ]);
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Transaksi berhasil di-void.');
    }
}

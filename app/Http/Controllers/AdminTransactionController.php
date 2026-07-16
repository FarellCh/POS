<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Product;
use App\Domains\Product\Models\StockHistory;
use App\Domains\Account\Models\User;
use App\Domains\Payment\Models\PaymentMethod;
use App\Domains\Transaction\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'cashier_id' => ['nullable', 'integer', 'exists:users,id'],
            'payment_method' => ['nullable', 'string', 'max:50', 'exists:payment_methods,code'],
        ]);

        $transactionsQuery = Transaction::query()
            ->with(['user', 'voidedBy'])
            ->latest('created_at');

        if (! empty($validated['date_from'])) {
            $transactionsQuery->where('created_at', '>=', Carbon::parse($validated['date_from'])->startOfDay());
        }

        if (! empty($validated['date_to'])) {
            $transactionsQuery->where('created_at', '<=', Carbon::parse($validated['date_to'])->endOfDay());
        }

        if (! empty($validated['cashier_id'])) {
            $transactionsQuery->where('user_id', $validated['cashier_id']);
        }

        if (! empty($validated['payment_method'])) {
            $paymentMethodLabel = PaymentMethod::query()
                ->where('code', $validated['payment_method'])
                ->value('label');

            if ($paymentMethodLabel) {
                $transactionsQuery->where('payment_method', $paymentMethodLabel);
            }
        }

        $transactions = $transactionsQuery
            ->paginate(15)
            ->withQueryString();

        $statisticsQuery = Transaction::query()->where('is_voided', false);

        if (! empty($validated['date_from'])) {
            $statisticsQuery->where('created_at', '>=', Carbon::parse($validated['date_from'])->startOfDay());
        }

        if (! empty($validated['date_to'])) {
            $statisticsQuery->where('created_at', '<=', Carbon::parse($validated['date_to'])->endOfDay());
        }

        if (! empty($validated['cashier_id'])) {
            $statisticsQuery->where('user_id', $validated['cashier_id']);
        }

        if (! empty($validated['payment_method'])) {
            $paymentMethodLabel = PaymentMethod::query()
                ->where('code', $validated['payment_method'])
                ->value('label');

            if ($paymentMethodLabel) {
                $statisticsQuery->where('payment_method', $paymentMethodLabel);
            }
        }

        $summary = [
            'total_transaksi' => (clone $statisticsQuery)->count(),
            'omzet' => (float) (clone $statisticsQuery)->sum('grand_total'),
            'paid' => (float) (clone $statisticsQuery)->sum('paid_amount'),
            'profit' => (float) ((clone $statisticsQuery)->sum('grand_total') - (clone $statisticsQuery)->sum('total_cost')),
        ];

        return view('admin.transactions', [
            'transactions' => $transactions,
            'cashiers' => User::query()
                ->where('role', 'cashier')
                ->orderBy('name')
                ->get(),
            'paymentMethods' => PaymentMethod::query()
                ->active()
                ->orderBy('label')
                ->get(),
            'filters' => [
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
                'cashier_id' => $validated['cashier_id'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
            ],
            'summary' => $summary,
        ]);
    }

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

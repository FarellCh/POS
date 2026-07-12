<?php

namespace App\Http\Controllers;

use App\Domains\Payment\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPaymentMethodController extends Controller
{
    public function index(): View
    {
        $paymentMethods = PaymentMethod::query()
            ->ordered()
            ->get();

        return view('admin.payment-methods', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'methods' => ['required', 'array'],
            'methods.*.label' => ['required', 'string', 'max:100'],
            'methods.*.is_active' => ['nullable', 'boolean'],
            'methods.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            foreach ($validated['methods'] as $methodId => $methodData) {
                PaymentMethod::query()
                    ->whereKey((int) $methodId)
                    ->update([
                        'label' => $methodData['label'],
                        'is_active' => $request->has("methods.{$methodId}.is_active"),
                        'sort_order' => $methodData['sort_order'],
                    ]);
            }
        });

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Pengaturan payment berhasil disimpan.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:payment_methods,code'],
            'label' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        PaymentMethod::create([
            'code' => Str::lower($validated['code']),
            'label' => $validated['label'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Metode payment baru berhasil ditambahkan.');
    }
}

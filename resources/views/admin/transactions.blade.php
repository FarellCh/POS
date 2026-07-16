@extends('layouts.pos')

@section('title', 'Riwayat Transaksi | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Riwayat Transaksi</p>
                <h1 class="mt-4 text-3xl font-semibold text-white sm:text-4xl">Lihat transaksi lengkap dengan filter tanggal, kasir, dan payment.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    Halaman ini dipakai buat monitoring transaksi masuk, void, dan ringkasan performa penjualan.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Kembali ke Admin
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Transaksi</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $summary['total_transaksi'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Omzet</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">Rp {{ number_format($summary['omzet'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Dibayar</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">Rp {{ number_format($summary['paid'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Profit</p>
                <p class="mt-2 text-3xl font-semibold text-amber-300">Rp {{ number_format($summary['profit'], 0, ',', '.') }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Filter</h2>
                <p class="text-sm text-slate-300">Saring data berdasarkan periode dan metode bayar.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.transactions.index') }}" class="mt-5 grid gap-4 lg:grid-cols-4">
            <div>
                <label for="date_from" class="mb-2 block text-sm font-medium text-slate-200">Dari Tanggal</label>
                <input
                    id="date_from"
                    name="date_from"
                    type="date"
                    value="{{ $filters['date_from'] }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none"
                >
            </div>
            <div>
                <label for="date_to" class="mb-2 block text-sm font-medium text-slate-200">Sampai Tanggal</label>
                <input
                    id="date_to"
                    name="date_to"
                    type="date"
                    value="{{ $filters['date_to'] }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none"
                >
            </div>
            <div>
                <label for="cashier_id" class="mb-2 block text-sm font-medium text-slate-200">Kasir</label>
                <select id="cashier_id" name="cashier_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                    <option value="">Semua Kasir</option>
                    @foreach ($cashiers as $cashier)
                        <option value="{{ $cashier->id }}" @selected((string) $filters['cashier_id'] === (string) $cashier->id)>
                            {{ $cashier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="payment_method" class="mb-2 block text-sm font-medium text-slate-200">Payment</label>
                <select id="payment_method" name="payment_method" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                    <option value="">Semua Payment</option>
                    @foreach ($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->code }}" @selected($filters['payment_method'] === $paymentMethod->code)>
                            {{ $paymentMethod->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 lg:col-span-4">
                <button type="submit" class="rounded-2xl bg-cyan-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="rounded-2xl border border-white/10 bg-slate-950/45 px-5 py-3 font-semibold text-white transition hover:bg-slate-950/60">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Daftar Transaksi</h2>
                <p class="text-sm text-slate-300">Riwayat transaksi lengkap yang sudah masuk ke sistem.</p>
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                    <thead class="bg-slate-950/40 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Invoice</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Kasir</th>
                            <th class="px-4 py-3 font-medium">Payment</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                            <th class="px-4 py-3 font-medium">Dibayar</th>
                            <th class="px-4 py-3 font-medium">Kembalian</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 bg-slate-950/20 text-slate-100">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 text-slate-300">{{ $transaction->invoice_number }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $transaction->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $transaction->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $transaction->payment_method }}</td>
                                <td class="px-4 py-3">Rp {{ number_format((float) $transaction->grand_total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format((float) $transaction->paid_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format((float) $transaction->change_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    @if ($transaction->is_voided)
                                        <span class="rounded-full border border-rose-400/30 bg-rose-400/10 px-3 py-1 text-xs font-semibold text-rose-200">Void</span>
                                    @else
                                        <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">Lunas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if (! $transaction->is_voided)
                                        <form method="POST" action="{{ route('admin.transactions.void', $transaction) }}" class="flex flex-col gap-2">
                                            @csrf
                                            <input
                                                type="text"
                                                name="void_reason"
                                                value="{{ old('void_reason') }}"
                                                class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-rose-400/50 focus:outline-none"
                                                placeholder="Alasan void"
                                                required
                                            >
                                            <button type="submit" class="rounded-2xl bg-rose-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-400">
                                                Void
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-xs text-slate-400">Di-void oleh {{ $transaction->voidedBy?->name ?? '-' }}</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-slate-400">
                                    Tidak ada transaksi dengan filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $transactions->links() }}
        </div>
    </section>
</div>
@endsection

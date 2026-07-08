@extends('layouts.pos')

@section('title', 'Admin | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-emerald-300">Admin Console</p>
                <h1 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Panel admin untuk kontrol data dan transaksi.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    Di sini admin bisa lihat ringkasan master data, transaksi, dan arah pengembangan modul berikutnya.
                </p>
            </div>
            <a href="{{ route('kasir.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                Buka Kasir
            </a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Kategori</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $statistics['kategori'] }}</p>
            </div>
            <div class="rounded-2xl bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Produk</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $statistics['produk'] }}</p>
            </div>
            <div class="rounded-2xl bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Produk Nonaktif</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">{{ $statistics['produk_nonaktif'] }}</p>
            </div>
            <div class="rounded-2xl bg-slate-950/40 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Transaksi</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $statistics['transaksi_total'] }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Transaksi Terbaru</h2>
                <p class="text-sm text-slate-300">Ringkasan transaksi untuk monitoring awal.</p>
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                <thead class="bg-slate-950/40 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 font-medium">Invoice</th>
                        <th class="px-4 py-3 font-medium">Kasir</th>
                        <th class="px-4 py-3 font-medium">Metode</th>
                        <th class="px-4 py-3 font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-950/20 text-slate-100">
                    @forelse ($recentTransactions as $transaction)
                        <tr>
                            <td class="px-4 py-3 text-slate-300">{{ $transaction->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $transaction->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $transaction->payment_method }}</td>
                            <td class="px-4 py-3">Rp {{ number_format((float) $transaction->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

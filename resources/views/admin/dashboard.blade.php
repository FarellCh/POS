@extends('layouts.pos')

@section('title', 'Admin | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-400/15 text-emerald-300">A</span>
                    <p class="text-sm uppercase tracking-[0.35em] text-emerald-300">Admin Panel</p>
                </div>
                <h1 class="mt-4 text-3xl font-semibold text-white sm:text-4xl">Panel admin untuk kontrol master data, transaksi, dan stok.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    Tampilan ini disiapkan sebagai pusat monitoring operasional, dengan fokus ke ringkasan data, stok menipis, dan transaksi terakhir.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                <a href="{{ route('kasir.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Buka Kasir
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-rose-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-400">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Kategori</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $statistics['kategori'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Produk</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $statistics['produk'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Produk Nonaktif</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">{{ $statistics['produk_nonaktif'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Transaksi</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $statistics['transaksi_total'] }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Transaksi Terbaru</h2>
                        <p class="text-sm text-slate-300">Monitoring transaksi paling baru masuk.</p>
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
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Produk Terbaru</h2>
                        <p class="text-sm text-slate-300">Daftar cepat item baru yang masuk ke sistem.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($recentProducts as $product)
                        <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ $product->sku }}</p>
                                    <h3 class="mt-2 text-base font-semibold text-white">{{ $product->name }}</h3>
                                </div>
                                <span class="rounded-full bg-white/5 px-2 py-1 text-xs text-slate-300">{{ $product->category?->name ?? '-' }}</span>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-slate-400">Stok</span>
                                <span class="font-semibold text-white">{{ $product->stock }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-slate-400">
                            Belum ada produk.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <h2 class="text-xl font-semibold text-white">Stok Menipis</h2>
                <p class="mt-2 text-sm text-slate-300">Produk aktif dengan stok paling rendah.</p>

                <div class="mt-5 space-y-3">
                    @forelse ($lowStockProducts as $product)
                        <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $product->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $product->category?->name ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Stok</p>
                                    <p class="text-lg font-semibold text-amber-300">{{ $product->stock }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-slate-400">
                            Tidak ada stok menipis.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-950/45 to-slate-950/20 p-6 shadow-xl backdrop-blur-xl">
                <p class="text-xs uppercase tracking-[0.35em] text-cyan-300">Quick Notes</p>
                <h3 class="mt-2 text-lg font-semibold text-white">Admin workflow</h3>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3">Kelola kategori, produk, dan stok dari sini.</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3">Pantau transaksi harian dan item yang perlu restock.</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3">Logout cepat tersedia di header atas.</div>
                </div>
            </div>
        </aside>
    </section>
</div>
@endsection

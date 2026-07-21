@extends('layouts.pos')

@section('title', 'Riwayat Stok Produk | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Riwayat Stok Produk</p>
                <h1 class="mt-4 text-3xl font-semibold text-white sm:text-4xl">{{ $product->name }}</h1>
                <p class="mt-2 text-sm text-slate-300">
                    {{ $product->sku }} • {{ $product->category?->name ?? 'Tanpa kategori' }} • Stok sekarang {{ $product->stock }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Kembali ke Stok
                </a>
                <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Edit Barang
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Catatan</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $summary['total_catatan'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Masuk</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">{{ $summary['total_masuk'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Keluar</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">{{ $summary['total_keluar'] }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Filter Riwayat</h2>
                <p class="text-sm text-slate-300">Saring berdasarkan tanggal dan tipe stok.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.inventory.products.history', $product) }}" class="mt-5 grid gap-4 lg:grid-cols-3">
            <div>
                <label for="date_from" class="mb-2 block text-sm font-medium text-slate-200">Dari Tanggal</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
            </div>
            <div>
                <label for="date_to" class="mb-2 block text-sm font-medium text-slate-200">Sampai Tanggal</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
            </div>
            <div>
                <label for="type" class="mb-2 block text-sm font-medium text-slate-200">Tipe</label>
                <select id="type" name="type" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                    <option value="">Semua Tipe</option>
                    <option value="purchase" @selected($filters['type'] === 'purchase')>Purchase / Stok Masuk</option>
                    <option value="opname" @selected($filters['type'] === 'opname')>Opname</option>
                    <option value="in" @selected($filters['type'] === 'in')>Masuk</option>
                    <option value="out" @selected($filters['type'] === 'out')>Keluar</option>
                    <option value="damaged" @selected($filters['type'] === 'damaged')>Rusak</option>
                    <option value="lost" @selected($filters['type'] === 'lost')>Hilang</option>
                </select>
            </div>

            <div class="flex items-end gap-3 lg:col-span-3">
                <button type="submit" class="rounded-2xl bg-cyan-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.inventory.products.history', $product) }}" class="rounded-2xl border border-white/10 bg-slate-950/45 px-5 py-3 font-semibold text-white transition hover:bg-slate-950/60">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Ledger Stok</h2>
                <p class="text-sm text-slate-300">Mutasi stok per produk yang bisa ditelusuri.</p>
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                    <thead class="bg-slate-950/40 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Waktu</th>
                            <th class="px-4 py-3 font-medium">Tipe</th>
                            <th class="px-4 py-3 font-medium">Qty</th>
                            <th class="px-4 py-3 font-medium">Sebelum</th>
                            <th class="px-4 py-3 font-medium">Sesudah</th>
                            <th class="px-4 py-3 font-medium">Supplier</th>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Referensi</th>
                            <th class="px-4 py-3 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 bg-slate-950/20 text-slate-100">
                        @forelse ($history as $item)
                            @php
                                $badgeClass = match ($item->type) {
                                    'purchase', 'in' => 'bg-emerald-400/10 text-emerald-300 border-emerald-400/20',
                                    'out' => 'bg-cyan-400/10 text-cyan-300 border-cyan-400/20',
                                    'opname' => 'bg-amber-400/10 text-amber-300 border-amber-400/20',
                                    'damaged', 'lost' => 'bg-rose-400/10 text-rose-300 border-rose-400/20',
                                    default => 'bg-slate-400/10 text-slate-300 border-slate-400/20',
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-slate-300">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                                        {{ strtoupper($item->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3">{{ $item->before_stock ?? 0 }}</td>
                                <td class="px-4 py-3">{{ $item->after_stock ?? 0 }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $item->supplier?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $item->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $item->reference_number ?? $item->reference ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $item->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-slate-400">
                                    Belum ada riwayat stok untuk produk ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $history->links() }}
        </div>
    </section>
</div>
@endsection

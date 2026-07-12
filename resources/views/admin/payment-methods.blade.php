@extends('layouts.pos')

@section('title', 'Setting Payment | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Payment Settings</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Atur metode pembayaran kasir</h1>
                <p class="mt-2 text-sm text-slate-300">Metode yang aktif di sini akan tampil di panel kasir.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.cashier-sessions.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Kembali ke Data Kasir
                </a>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Kembali ke Admin
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Metode</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $paymentMethods->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Aktif</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">{{ $paymentMethods->where('is_active', true)->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Nonaktif</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">{{ $paymentMethods->where('is_active', false)->count() }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Metode yang digunakan kasir</h2>
                <p class="text-sm text-slate-300">Ubah label, urutan, atau aktif/nonaktif setiap metode.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.payment-methods.update') }}" class="mt-6 space-y-4">
            @csrf

            <div class="overflow-hidden rounded-2xl border border-white/10">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                    <thead class="bg-slate-950/40 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Label</th>
                            <th class="px-4 py-3 font-medium">Urutan</th>
                            <th class="px-4 py-3 font-medium">Aktif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 bg-slate-950/20 text-slate-100">
                        @forelse ($paymentMethods as $paymentMethod)
                            <tr>
                                <td class="px-4 py-3 align-top">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">
                                        {{ $paymentMethod->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <input
                                        type="text"
                                        name="methods[{{ $paymentMethod->id }}][label]"
                                        value="{{ $paymentMethod->label }}"
                                        class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                    >
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <input
                                        type="number"
                                        min="0"
                                        name="methods[{{ $paymentMethod->id }}][sort_order]"
                                        value="{{ $paymentMethod->sort_order }}"
                                        class="w-28 rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                    >
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-200">
                                        <input
                                            type="checkbox"
                                            name="methods[{{ $paymentMethod->id }}][is_active]"
                                            value="1"
                                            @checked($paymentMethod->is_active)
                                            class="rounded border-white/20 bg-slate-950/45 text-cyan-400 focus:ring-cyan-400/30"
                                        >
                                        Aktif di kasir
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                    Belum ada metode payment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button type="submit" class="rounded-2xl bg-cyan-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                Simpan Pengaturan
            </button>
        </form>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-xl font-semibold text-white">Tambah metode payment</h2>
            <p class="mt-2 text-sm text-slate-300">Gunakan kalau nanti ada metode baru di luar default.</p>
        </div>

        <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="mt-6 grid gap-4 lg:grid-cols-4">
            @csrf

            <div>
                <label for="code" class="mb-2 block text-sm font-medium text-slate-200">Code</label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                    placeholder="contoh: qris"
                    required
                >
            </div>

            <div>
                <label for="new_label" class="mb-2 block text-sm font-medium text-slate-200">Label</label>
                <input
                    id="new_label"
                    name="label"
                    type="text"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                    placeholder="contoh: QRIS"
                    required
                >
            </div>

            <div>
                <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-200">Urutan</label>
                <input
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    value="0"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                >
            </div>

            <div class="flex flex-col gap-3">
                <label class="mt-9 inline-flex items-center gap-2 text-sm text-slate-200">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-white/20 bg-slate-950/45 text-cyan-400 focus:ring-cyan-400/30">
                    Aktif di kasir
                </label>
                <button type="submit" class="rounded-2xl bg-emerald-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-emerald-400">
                    Tambah Metode
                </button>
            </div>
        </form>
    </section>
</div>
@endsection

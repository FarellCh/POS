@extends('layouts.pos')

@section('title', 'Data Kasir | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Data Kasir</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Riwayat login dan durasi kerja kasir</h1>
                <p class="mt-2 text-sm text-slate-300">Halaman ini menampilkan sesi login kasir, waktu mulai, waktu selesai, dan total durasi kerja.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="#create-cashier-form" class="inline-flex items-center justify-center rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/15">
                    Buat User
                </a>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                    Kembali ke Admin
                </a>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Sesi</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $statistics['total_sesi'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Sesi Aktif</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">{{ $statistics['sesi_aktif'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Kasir Tercatat</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $statistics['kasir_tercatat'] }}</p>
            </div>
        </div>
    </section>

    <section id="create-cashier-form" class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-emerald-300">Daftar Akun</p>
                <h2 class="mt-3 text-2xl font-semibold text-white">Buat akun cashier baru</h2>
                <p class="mt-2 text-sm text-slate-300">Form ini khusus untuk mendaftarkan user dengan role cashier.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.cashier-users.store') }}" class="mt-6 grid gap-4 lg:grid-cols-2">
            @csrf

            @if ($errors->any())
                <div class="lg:col-span-2 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-sm text-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-200">Nama Lengkap</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                        placeholder="Contoh: Budi Pratama"
                        required
                    >
                </div>

                <div>
                    <label for="username" class="mb-2 block text-sm font-medium text-slate-200">Username</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                        placeholder="Contoh: budi_kasir"
                        required
                    >
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                        placeholder="contoh@email.com"
                        required
                    >
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                            placeholder="Minimal 8 karakter"
                            required
                        >
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-200">Ulangi Password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                            placeholder="Konfirmasi password"
                            required
                        >
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Role</p>
                    <p class="mt-2 text-lg font-semibold text-white">Cashier</p>
                    <p class="mt-1 text-sm text-slate-400">Role otomatis diset ke cashier saat disimpan.</p>
                </div>

                <button type="submit" class="w-full rounded-2xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                    Simpan Akun Kasir
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Riwayat Login Kasir</h2>
                <p class="text-sm text-slate-300">Data terbaru tampil paling atas.</p>
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                <thead class="bg-slate-950/40 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 font-medium">Kasir</th>
                        <th class="px-4 py-3 font-medium">Mulai Login</th>
                        <th class="px-4 py-3 font-medium">Selesai Login</th>
                        <th class="px-4 py-3 font-medium">Durasi</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-950/20 text-slate-100">
                    @forelse ($sessions as $session)
                        @php
                            $durationSeconds = $session->duration_seconds;
                            if ($durationSeconds === null && $session->started_at && $session->ended_at === null) {
                                $durationSeconds = $session->started_at->diffInSeconds(now());
                            }

                            $hours = str_pad((string) intdiv((int) $durationSeconds, 3600), 2, '0', STR_PAD_LEFT);
                            $minutes = str_pad((string) intdiv(((int) $durationSeconds % 3600), 60), 2, '0', STR_PAD_LEFT);
                            $seconds = str_pad((string) ((int) $durationSeconds % 60), 2, '0', STR_PAD_LEFT);
                            $formattedDuration = "{$hours}:{$minutes}:{$seconds}";
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-white">{{ $session->user?->name ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $session->user?->username ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-300">{{ optional($session->started_at)->format('d M Y, H:i:s') }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $session->ended_at?->format('d M Y, H:i:s') ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-white">{{ $formattedDuration }}</td>
                            <td class="px-4 py-3">
                                @if ($session->ended_at)
                                    <span class="rounded-full border border-slate-500/30 bg-slate-500/10 px-3 py-1 text-xs font-semibold text-slate-300">Selesai</span>
                                @else
                                    <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                Belum ada data sesi kasir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | KyoraPOS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/8 p-8 shadow-2xl backdrop-blur-xl">
            <p class="text-xs uppercase tracking-[0.35em] text-cyan-300">KyoraPOS</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Login Kasir</h1>
            <p class="mt-2 text-sm leading-6 text-slate-300">Masuk pakai username dan password. Tidak ada register.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-sm text-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="username" class="mb-2 block text-sm font-medium text-slate-200">Username</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                        placeholder="Masukkan username"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                        placeholder="Masukkan password"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400"
                >
                    Login
                </button>
            </form>

            <div class="mt-6 rounded-2xl border border-white/10 bg-slate-950/35 p-4 text-sm text-slate-300">
                Contoh akun seed:
                <div class="mt-2 space-y-1 text-slate-200">
                    <div><span class="text-slate-400">Username:</span> admin</div>
                    <div><span class="text-slate-400">Password:</span> password</div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

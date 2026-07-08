<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KyoraPOS')</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.14), transparent 24%),
                linear-gradient(180deg, #0f172a 0%, #111827 55%, #0b1120 100%);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>

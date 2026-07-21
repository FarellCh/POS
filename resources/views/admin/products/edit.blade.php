@extends('layouts.pos')

@section('title', 'Edit Barang | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Edit Barang</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ $product->name }}</h1>
                <p class="mt-2 text-sm text-slate-300">Perbarui data barang dan tambahkan stok jika perlu.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                Kembali ke Admin
            </a>
            <a href="{{ route('admin.inventory.products.history', $product) }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-medium text-white transition hover:bg-white/15">
                Riwayat Stok
            </a>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="grid gap-4 lg:grid-cols-2">
            @csrf
            @method('PATCH')

            <div>
                <label for="category_id" class="mb-2 block text-sm font-medium text-slate-200">Kategori</label>
                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none"
                >
                    <option value="">Tanpa kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sku" class="mb-2 block text-sm font-medium text-slate-200">SKU</label>
                <input
                    id="sku"
                    name="sku"
                    type="text"
                    value="{{ old('sku', $product->sku) }}"
                    maxlength="50"
                    required
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                >
            </div>

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-200">Nama Barang</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $product->name) }}"
                    maxlength="150"
                    required
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                >
            </div>

            <div>
                <label for="cost_price" class="mb-2 block text-sm font-medium text-slate-200">Cost Price</label>
                <input
                    id="cost_price"
                    name="cost_price"
                    type="number"
                    min="0"
                    step="0.01"
                    value="{{ old('cost_price', $product->cost_price) }}"
                    required
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                >
            </div>

            <div>
                <label for="selling_price" class="mb-2 block text-sm font-medium text-slate-200">Selling Price</label>
                <input
                    id="selling_price"
                    name="selling_price"
                    type="number"
                    min="0"
                    step="0.01"
                    value="{{ old('selling_price', $product->selling_price) }}"
                    required
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                >
            </div>

            <div>
                <label for="stock_adjustment" class="mb-2 block text-sm font-medium text-slate-200">Tambah Stok</label>
                <input
                    id="stock_adjustment"
                    name="stock_adjustment"
                    type="number"
                    min="0"
                    step="1"
                    value="{{ old('stock_adjustment', 0) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                >
                <p class="mt-2 text-xs text-slate-400">Isi kalau ingin menambah stok. Stok saat ini: {{ $product->stock }}</p>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-300 lg:col-span-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-white/20 bg-slate-950/45 text-cyan-400 focus:ring-cyan-400/30">
                Barang aktif
            </label>

            <div class="flex flex-col gap-3 lg:col-span-2 lg:flex-row">
                <button type="submit" class="rounded-2xl bg-cyan-500 px-5 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 py-3 font-semibold text-white transition hover:bg-white/15">
                    Batal
                </a>
            </div>
        </form>
    </section>
</div>
@endsection

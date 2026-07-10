@extends('layouts.pos')

@section('title', 'Admin | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

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
                        <button
                            type="button"
                            class="product-pick text-left rounded-2xl border border-white/10 bg-slate-950/35 p-4 transition hover:border-cyan-400/30 hover:bg-slate-950/50"
                            data-product-id="{{ $product->id }}"
                            data-product='@json([
                                "id" => $product->id,
                                "category_id" => $product->category_id,
                                "category_name" => $product->category?->name ?? null,
                                "sku" => $product->sku,
                                "name" => $product->name,
                                "cost_price" => (float) $product->cost_price,
                                "selling_price" => (float) $product->selling_price,
                                "stock" => $product->stock,
                                "is_active" => $product->is_active,
                            ])'
                        >
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
                        </button>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Tambah Kategori</h2>
                        <p class="text-sm text-slate-300">Kategori baru langsung muncul di dropdown barang.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-sm text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="category_name" class="mb-2 block text-sm font-medium text-slate-200">Nama Kategori</label>
                        <input
                            id="category_name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            maxlength="100"
                            required
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                            placeholder="Contoh: Minuman"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-emerald-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-emerald-400"
                    >
                        Simpan Kategori
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Tambah Barang</h2>
                        <p class="text-sm text-slate-300">Klik produk di sebelah kiri untuk isi otomatis.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-sm text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.products.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <input type="hidden" name="product_id" id="product_id" value="{{ old('product_id') }}">

                    <div id="product-selection" class="hidden rounded-2xl border border-cyan-400/20 bg-cyan-400/10 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-cyan-200">Produk terpilih</p>
                                <p id="selected-product-name" class="mt-1 text-base font-semibold text-white">-</p>
                                <p id="selected-product-meta" class="mt-1 text-sm text-slate-300">-</p>
                            </div>
                            <button
                                type="button"
                                id="clear-selected-product"
                                class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10"
                            >
                                Batal
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="category_id" class="mb-2 block text-sm font-medium text-slate-200">Kategori</label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none"
                        >
                            <option value="">Tanpa kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="sku" class="mb-2 block text-sm font-medium text-slate-200">SKU</label>
                            <input
                                id="sku"
                                name="sku"
                                type="text"
                                value="{{ old('sku') }}"
                                maxlength="50"
                                required
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                placeholder="SKU barang"
                            >
                        </div>
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-200">Nama Barang</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                maxlength="150"
                                required
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                placeholder="Nama barang"
                            >
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="cost_price" class="mb-2 block text-sm font-medium text-slate-200">Cost Price</label>
                            <input
                                id="cost_price"
                                name="cost_price"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('cost_price') }}"
                                required
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                placeholder="0"
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
                                value="{{ old('selling_price') }}"
                                required
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                placeholder="0"
                            >
                        </div>
                        <div>
                            <label for="stock" class="mb-2 block text-sm font-medium text-slate-200">Stock</label>
                            <input
                                id="stock"
                                name="stock"
                                type="number"
                                min="0"
                                step="1"
                                value="{{ old('stock', 0) }}"
                                required
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white placeholder:text-slate-500 focus:border-cyan-400/50 focus:outline-none"
                                placeholder="0"
                            >
                            <p class="mt-2 text-xs text-slate-400" id="stock-help">Isi stok awal untuk barang baru.</p>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-white/20 bg-slate-950/45 text-cyan-400 focus:ring-cyan-400/30">
                        Barang aktif
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400"
                    >
                        Simpan Barang
                    </button>
                </form>
            </div>

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

        </aside>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const productCards = document.querySelectorAll('.product-pick');
    const productIdInput = document.getElementById('product_id');
    const productSelection = document.getElementById('product-selection');
    const selectedProductName = document.getElementById('selected-product-name');
    const selectedProductMeta = document.getElementById('selected-product-meta');
    const clearSelectedProduct = document.getElementById('clear-selected-product');
    const stockHelp = document.getElementById('stock-help');

    const categorySelect = document.getElementById('category_id');
    const skuInput = document.getElementById('sku');
    const nameInput = document.getElementById('name');
    const costPriceInput = document.getElementById('cost_price');
    const sellingPriceInput = document.getElementById('selling_price');
    const stockInput = document.getElementById('stock');
    const isActiveInput = document.querySelector('input[name="is_active"]');

    const categoryOptionById = (categoryId) => {
        return categorySelect.querySelector(`option[value="${categoryId ?? ''}"]`);
    };

    const resetSelection = () => {
        productIdInput.value = '';
        productSelection.classList.add('hidden');
        selectedProductName.textContent = '-';
        selectedProductMeta.textContent = '-';
        stockHelp.textContent = 'Isi stok awal untuk barang baru.';
        productCards.forEach((card) => {
            card.classList.remove('border-cyan-400/40', 'bg-cyan-400/10');
        });
        categorySelect.value = '';
        skuInput.value = '';
        nameInput.value = '';
        costPriceInput.value = '';
        sellingPriceInput.value = '';
        stockInput.value = '0';
        if (isActiveInput) {
            isActiveInput.checked = true;
        }
    };

    const fillForm = (product) => {
        productIdInput.value = product.id;
        productSelection.classList.remove('hidden');
        selectedProductName.textContent = product.name;
        selectedProductMeta.textContent = `${product.sku} - ${product.category_name ?? 'Tanpa kategori'} - Stok saat ini: ${product.stock}`;
        stockHelp.textContent = 'Masukkan jumlah stok yang mau ditambahkan ke produk ini.';
        productCards.forEach((card) => {
            card.classList.remove('border-cyan-400/40', 'bg-cyan-400/10');
        });

        const activeCard = document.querySelector(`.product-pick[data-product-id="${product.id}"]`);
        if (activeCard) {
            activeCard.classList.add('border-cyan-400/40', 'bg-cyan-400/10');
        }

        if (categoryOptionById(product.category_id)) {
            categorySelect.value = String(product.category_id ?? '');
        } else {
            categorySelect.value = '';
        }

        skuInput.value = product.sku ?? '';
        nameInput.value = product.name ?? '';
        costPriceInput.value = product.cost_price ?? '';
        sellingPriceInput.value = product.selling_price ?? '';
        stockInput.value = '0';
        if (isActiveInput) {
            isActiveInput.checked = Boolean(product.is_active);
        }

        stockInput.focus();
        stockInput.select();
    };

    productCards.forEach((card) => {
        card.addEventListener('click', () => {
            const product = JSON.parse(card.dataset.product);
            fillForm(product);
        });
    });

    clearSelectedProduct?.addEventListener('click', resetSelection);

    if (productIdInput.value) {
        const selectedCard = document.querySelector(`.product-pick[data-product-id="${productIdInput.value}"]`);
        if (selectedCard) {
            fillForm(JSON.parse(selectedCard.dataset.product));
        }
    }
</script>
@endpush

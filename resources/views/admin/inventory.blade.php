@extends('layouts.pos')

@section('title', 'Manajemen Stok | KyoraPOS')

@section('content')
<div class="w-full space-y-6">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Manajemen Stok</p>
                <h1 class="mt-4 text-3xl font-semibold text-white sm:text-4xl">Stok masuk, opname, rusak/hilang, dan supplier dalam satu tempat.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    Halaman ini menjaga mutasi stok tetap rapi dan bisa ditelusuri per produk, supplier, dan user yang input.
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
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Supplier</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ $statistics['supplier'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Total Movement</p>
                <p class="mt-2 text-3xl font-semibold text-cyan-300">{{ $statistics['movement_total'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Stok Masuk / Pembelian</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">{{ $statistics['movement_in'] }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Opname / Rusak / Hilang</p>
                <p class="mt-2 text-3xl font-semibold text-amber-300">{{ $statistics['movement_opname'] + $statistics['movement_damage'] }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div>
                    <h2 class="text-xl font-semibold text-white">Tambah Supplier</h2>
                    <p class="text-sm text-slate-300">Supplier dipakai untuk pembelian barang dan histori stok masuk.</p>
                </div>

                <form method="POST" action="{{ route('admin.inventory.suppliers.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="supplier_name">Nama Supplier</label>
                        <input id="supplier_name" name="supplier_name" type="text" value="{{ old('supplier_name') }}" maxlength="150" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="Contoh: PT Sumber Makmur">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="supplier_phone">Telepon</label>
                            <input id="supplier_phone" name="supplier_phone" type="text" value="{{ old('supplier_phone') }}" maxlength="30" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="0812...">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="supplier_email">Email</label>
                            <input id="supplier_email" name="supplier_email" type="email" value="{{ old('supplier_email') }}" maxlength="150" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="supplier@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="supplier_address">Alamat</label>
                        <textarea id="supplier_address" name="supplier_address" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="Alamat supplier">{{ old('supplier_address') }}</textarea>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="supplier_is_active" value="1" @checked(old('supplier_is_active', true)) class="rounded border-white/20 bg-slate-950/45 text-cyan-400 focus:ring-cyan-400/30">
                        Supplier aktif
                    </label>

                    <button type="submit" class="w-full rounded-2xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                        Simpan Supplier
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div>
                    <h2 class="text-xl font-semibold text-white">Stok Masuk / Pembelian Barang</h2>
                    <p class="text-sm text-slate-300">Tambah stok barang dari supplier dengan catatan pembelian.</p>
                </div>

                <form method="POST" action="{{ route('admin.inventory.purchases.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="purchase_product_id">Produk</label>
                        <select id="purchase_product_id" name="purchase_product_id" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('purchase_product_id') == $product->id)>
                                    {{ $product->sku }} — {{ $product->name }} (stok {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="purchase_supplier_id">Supplier</label>
                            <select id="purchase_supplier_id" name="purchase_supplier_id" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                                <option value="">Pilih supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('purchase_supplier_id') == $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="purchase_reference_number">No Referensi</label>
                            <input id="purchase_reference_number" name="purchase_reference_number" type="text" value="{{ old('purchase_reference_number') }}" maxlength="100" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="INV/PO supplier">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="purchase_quantity">Qty Masuk</label>
                            <input id="purchase_quantity" name="purchase_quantity" type="number" min="1" step="1" value="{{ old('purchase_quantity', 1) }}" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="purchase_unit_cost">Harga Beli / Unit</label>
                            <input id="purchase_unit_cost" name="purchase_unit_cost" type="number" min="0" step="0.01" value="{{ old('purchase_unit_cost') }}" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="0">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="purchase_notes">Catatan</label>
                        <textarea id="purchase_notes" name="purchase_notes" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="Catatan pembelian">{{ old('purchase_notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-emerald-400">
                        Simpan Stok Masuk
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div>
                    <h2 class="text-xl font-semibold text-white">Stok Opname</h2>
                    <p class="text-sm text-slate-300">Set stok sesuai hasil hitung fisik di lapangan.</p>
                </div>

                <form method="POST" action="{{ route('admin.inventory.opname.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="opname_product_id">Produk</label>
                        <select id="opname_product_id" name="opname_product_id" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('opname_product_id') == $product->id)>
                                    {{ $product->sku }} — {{ $product->name }} (stok {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="counted_stock">Stok Hasil Hitung</label>
                        <input id="counted_stock" name="opname_counted_stock" type="number" min="0" step="1" value="{{ old('opname_counted_stock') }}" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="0">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="opname_notes">Catatan</label>
                        <textarea id="opname_notes" name="opname_notes" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="Catatan opname">{{ old('opname_notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">
                        Simpan Opname
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
                <div>
                    <h2 class="text-xl font-semibold text-white">Stok Rusak / Hilang</h2>
                    <p class="text-sm text-slate-300">Kurangi stok untuk barang rusak atau hilang.</p>
                </div>

                <form method="POST" action="{{ route('admin.inventory.damage.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="damage_product_id">Produk</label>
                            <select id="damage_product_id" name="damage_product_id" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                                <option value="">Pilih produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('damage_product_id') == $product->id)>
                                        {{ $product->sku }} — {{ $product->name }} (stok {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="damage_type">Tipe</label>
                            <select id="damage_type" name="damage_type" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none">
                            <option value="damaged" @selected(old('damage_type') === 'damaged')>Rusak</option>
                            <option value="lost" @selected(old('damage_type') === 'lost')>Hilang</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="damage_quantity">Qty</label>
                        <input id="damage_quantity" name="damage_quantity" type="number" min="1" step="1" value="{{ old('damage_quantity', 1) }}" required class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="1">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="damage_notes">Catatan</label>
                        <textarea id="damage_notes" name="damage_notes" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-white focus:border-cyan-400/50 focus:outline-none" placeholder="Detail kerusakan / kehilangan">{{ old('damage_notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-rose-500 px-4 py-3 font-semibold text-white transition hover:bg-rose-400">
                        Simpan Rusak / Hilang
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Riwayat Stok Terbaru</h2>
                <p class="text-sm text-slate-300">Mutasi stok terakhir yang sudah dicatat.</p>
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                    <thead class="bg-slate-950/40 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Waktu</th>
                            <th class="px-4 py-3 font-medium">Produk</th>
                            <th class="px-4 py-3 font-medium">Tipe</th>
                            <th class="px-4 py-3 font-medium">Qty</th>
                            <th class="px-4 py-3 font-medium">Sebelum</th>
                            <th class="px-4 py-3 font-medium">Sesudah</th>
                            <th class="px-4 py-3 font-medium">Supplier</th>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 bg-slate-950/20 text-slate-100">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-4 py-3 text-slate-300">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white">{{ $movement->product?->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400">{{ $movement->product?->sku ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ strtoupper($movement->type) }}</td>
                                <td class="px-4 py-3">{{ $movement->quantity }}</td>
                                <td class="px-4 py-3">{{ $movement->before_stock }}</td>
                                <td class="px-4 py-3">{{ $movement->after_stock }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $movement->supplier?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $movement->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $movement->notes ?? $movement->reference_number ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-slate-400">
                                    Belum ada riwayat stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-white">Daftar Supplier</h2>
                <p class="text-sm text-slate-300">Supplier aktif yang tersimpan di sistem.</p>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($suppliers as $supplier)
                <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-white">{{ $supplier->name }}</p>
                            <p class="mt-1 text-sm text-slate-300">{{ $supplier->phone ?? '-' }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $supplier->is_active ? 'bg-emerald-400/10 text-emerald-300' : 'bg-slate-400/10 text-slate-300' }}">
                            {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">{{ $supplier->email ?? '-' }}</p>
                    <p class="mt-2 text-sm text-slate-400">{{ $supplier->address ?? '-' }}</p>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-slate-400">
                    Belum ada supplier.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

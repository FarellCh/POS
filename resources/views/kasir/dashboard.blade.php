@extends('layouts.pos')

@section('title', 'Kasir | KyoraPOS')

@section('content')
@php
    $currency = static fn (float $value): string => 'Rp ' . number_format($value, 0, ',', '.');
@endphp

<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div class="w-full rounded-3xl border border-white/10 bg-slate-950/30 p-5 shadow-inner">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-cyan-400/35 bg-slate-950/55 ring-1 ring-cyan-400/15">
                    <span class="text-lg font-semibold text-cyan-300">K</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Kasir Console</p>
                    <h1 class="mt-1 text-xl font-semibold text-white sm:text-2xl">Kasir Sample</h1>
                    <p class="mt-1 text-sm leading-5 text-slate-400">Budi Pratama • 01:24:08 sejak login</p>
                </div>
                <span class="flex shrink-0 items-center justify-center rounded-full border border-white/10 bg-slate-950/35 px-4 py-2 text-sm font-semibold leading-5 text-slate-300">
                    Coming Soon
                </span>
            </div>

            <p class="mt-4 max-w-xl text-sm leading-6 text-slate-400">
                Tracking durasi kerja akan aktif setelah sistem login kasir siap.
            </p>
        </div>
    </section>

    <section class="grid gap-0 rounded-3xl border border-white/10 bg-white/8 shadow-xl backdrop-blur-xl lg:grid-cols-[minmax(0,1.6fr)_1px_minmax(360px,0.9fr)]">
        <div class="p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">Daftar Produk</h2>
                    <p class="text-sm text-slate-300">Cari produk lalu klik untuk masuk ke panel kalkulator.</p>
                </div>

                <form method="GET" action="{{ route('kasir.dashboard') }}" class="w-full lg:max-w-md">
                    <label for="search" class="sr-only">Cari produk</label>
                    <div class="flex items-center gap-2 rounded-2xl border border-white/10 bg-slate-950/35 px-4 py-3">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3m1.8-5.4a7.3 7.3 0 11-14.6 0 7.3 7.3 0 0114.6 0z" />
                        </svg>
                        <input
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama, SKU, atau kategori"
                            class="w-full bg-transparent text-sm text-white placeholder:text-slate-500 focus:outline-none"
                        >
                        @if ($search !== '')
                            <a href="{{ route('kasir.dashboard') }}" class="rounded-full px-2 py-1 text-xs text-slate-400 transition hover:bg-white/5 hover:text-white">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3" id="product-grid">
                @forelse ($products as $product)
                    <button
                        type="button"
                        class="product-card group rounded-2xl border border-white/10 bg-slate-950/25 p-4 text-left transition hover:-translate-y-0.5 hover:border-cyan-400/50 hover:bg-slate-950/45"
                        data-product='@json($product)'
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ $product['sku'] }}</p>
                                <h3 class="mt-2 text-base font-semibold text-white group-hover:text-cyan-200">{{ $product['name'] }}</h3>
                            </div>
                            <span class="rounded-full bg-white/5 px-2 py-1 text-xs text-slate-300">{{ $product['category'] }}</span>
                        </div>

                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <p class="text-xs text-slate-400">Stok</p>
                                <p class="text-lg font-semibold text-white">{{ $product['stock'] }}</p>
                            </div>
                            <p class="text-right text-sm font-medium text-emerald-300">{{ $currency($product['price']) }}</p>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-slate-950/25 p-6 text-center text-slate-400">
                        Produk tidak ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="hidden bg-white/10 lg:block"></div>

        <aside class="p-6">
            <div class="mt-5 rounded-3xl border border-white/10 bg-slate-950/30 p-4">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Qty</p>
                    <button
                        type="button"
                        id="clear-product"
                        class="rounded-full border border-white/10 bg-slate-950/35 px-3 py-2 text-xs font-medium text-slate-300 transition hover:border-white/20 hover:text-white"
                    >
                        Clear
                    </button>
                </div>

                <div class="mt-3 min-w-0 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/45 px-4 py-3 text-right">
                    <p id="quantity-display" class="truncate font-mono text-2xl font-semibold tabular-nums text-white">1</p>
                </div>

                <div class="mt-4 grid grid-cols-4 gap-2">
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="7">7</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="8">8</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="9">9</button>
                    <button type="button" class="calc-action rounded-2xl bg-white/5 py-4 text-sm font-medium text-slate-300 hover:bg-white/10" data-action="back">⌫</button>

                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="4">4</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="5">5</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="6">6</button>
                    <button type="button" class="calc-action rounded-2xl bg-white/5 py-4 text-sm font-medium text-slate-300 hover:bg-white/10" data-action="clear">C</button>

                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="1">1</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="2">2</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="3">3</button>
                    <button type="button" class="calc-action rounded-2xl bg-cyan-400/15 py-4 text-sm font-medium text-cyan-200 hover:bg-cyan-400/25" data-action="increment">+</button>

                    <button type="button" class="calc-key col-span-2 rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="0">0</button>
                    <button type="button" class="calc-key rounded-2xl bg-white/5 py-4 text-lg font-semibold text-white hover:bg-white/10" data-key="00">00</button>
                </div>

                <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-400">Subtotal</span>
                        <span id="subtotal-display" class="font-semibold text-white">Rp 0</span>
                    </div>
                </div>
            </div>
        </aside>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(value);

    const state = {
        selectedProduct: null,
        quantityInput: '',
    };

    const quantityDisplay = document.getElementById('quantity-display');
    const subtotalDisplay = document.getElementById('subtotal-display');

    const normalizeQuantityInput = (value) => value.replace(/\D/g, '').replace(/^0+/, '');

    const getQuantity = () => {
        const parsedQuantity = parseInt(state.quantityInput || '1', 10);
        return Number.isNaN(parsedQuantity) || parsedQuantity < 1 ? 1 : parsedQuantity;
    };

    const render = () => {
        const quantity = getQuantity();
        quantityDisplay.textContent = state.quantityInput || '1';
        subtotalDisplay.textContent = state.selectedProduct
            ? `Rp ${formatCurrency(state.selectedProduct.price * quantity)}`
            : 'Rp 0';
    };

    document.querySelectorAll('.product-card').forEach((button) => {
        button.addEventListener('click', () => {
            state.selectedProduct = JSON.parse(button.dataset.product);
            state.quantityInput = '';
            render();
        });
    });

    document.querySelectorAll('.calc-key').forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.key;
            const nextValue = normalizeQuantityInput(`${state.quantityInput}${key}`);
            state.quantityInput = nextValue === '' ? '1' : nextValue;
            render();
        });
    });

    document.querySelectorAll('.calc-action').forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.dataset.action;

            if (action === 'clear') {
                state.quantityInput = '';
                render();
                return;
            }

            if (action === 'back') {
                state.quantityInput = state.quantityInput.slice(0, -1);
                if (state.quantityInput === '0') {
                    state.quantityInput = '';
                }
                render();
                return;
            }

            if (action === 'increment') {
                state.quantityInput = String(getQuantity() + 1);
                render();
            }
        });
    });

    document.getElementById('clear-product').addEventListener('click', () => {
        state.selectedProduct = null;
        state.quantityInput = '';
        render();
    });

    render();
</script>
@endpush

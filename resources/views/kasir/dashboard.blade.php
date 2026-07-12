@extends('layouts.pos')

@section('title', 'Kasir | KyoraPOS')

@section('content')
@php
    $currency = static fn (float $value): string => 'Rp ' . number_format($value, 0, ',', '.');
@endphp

<div class="w-full space-y-6">
    <section class="rounded-3xl border border-white/10 bg-white/8 p-6 shadow-2xl backdrop-blur-xl">
        <div
            class="w-full rounded-3xl border border-white/10 bg-slate-950/30 p-5 shadow-inner"
            data-session-started-at="{{ $activeCashierSession?->started_at?->toIso8601String() }}"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-cyan-400/35 bg-slate-950/55 ring-1 ring-cyan-400/15">
                    <span class="text-lg font-semibold text-cyan-300">{{ strtoupper(mb_substr($cashier?->name ?? 'K', 0, 1)) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Kasir Console</p>
                    <h1 class="mt-1 text-xl font-semibold text-white sm:text-2xl">{{ $cashier?->name ?? 'Kasir Sample' }}</h1>
                    <p id="cashier-session-elapsed" class="mt-1 text-sm leading-5 text-slate-400">
                        {{ $cashierElapsedSeconds !== null ? gmdate('H:i:s', $cashierElapsedSeconds) . ' sejak login' : '00:00:00 sejak login' }}
                    </p>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-rose-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-400"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
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
                        data-product-id="{{ $product['id'] }}"
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
                                <p class="text-lg font-semibold text-white" data-stock-display="{{ $product['id'] }}">{{ $product['stock'] }}</p>
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
                <p id="stock-warning" class="mt-2 hidden text-sm text-rose-300">
                    Qty melebihi stok tersedia.
                </p>

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
                    <button
                        type="button"
                        id="confirm-order"
                        class="rounded-2xl bg-emerald-500 py-4 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-400"
                        disabled
                    >
                        Confirm
                    </button>
                </div>

                <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-400">Subtotal</span>
                        <span id="subtotal-display" class="font-semibold text-white">Rp 0</span>
                    </div>
                </div>
            </div>

            <div id="receipt-panel" class="mt-5 hidden rounded-3xl border border-white/10 bg-slate-950/30 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-300">Pembayaran</p>
                        <h3 class="text-lg font-semibold text-white">Pilih metode dulu</h3>
                    </div>
                    <button
                        type="button"
                        id="print-receipt"
                        class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40"
                        disabled
                    >
                        Print
                    </button>
                </div>

                <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/45 p-4">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($paymentMethods as $paymentMethod)
                            <button
                                type="button"
                                class="payment-method rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10"
                                data-payment-method="{{ $paymentMethod->code }}"
                            >
                                {{ $paymentMethod->label }}
                            </button>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 px-4 py-3 text-sm text-slate-400">
                                Belum ada metode payment aktif.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 flex items-center justify-between rounded-2xl border border-cyan-400/15 bg-slate-950/45 px-4 py-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Metode aktif</p>
                            <p id="receipt-payment-method" class="mt-1 font-semibold text-white">-</p>
                            <p id="receipt-payment-number" class="mt-1 hidden text-sm text-slate-300"></p>
                        </div>
                        <p id="payment-hint" class="text-sm text-slate-400">Pilih metode pembayaran dulu untuk menampilkan struk.</p>
                    </div>

                    <div id="receipt-body" class="hidden">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Invoice</p>
                                <p id="receipt-invoice" class="mt-1 font-semibold text-white">-</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Kasir</p>
                                <p class="mt-1 font-semibold text-white">Budi Pratama</p>
                            </div>
                        </div>

                        <div id="receipt-items" class="mt-4 space-y-3"></div>

                        <div class="mt-4 border-t border-white/10 pt-4 space-y-2 text-sm">
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Total Item</span>
                                <span id="receipt-total-item" class="font-semibold text-white">0</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Grand Total</span>
                                <span id="receipt-grand-total" class="font-semibold text-emerald-300">Rp 0</span>
                            </div>
                        </div>
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
    const formatDuration = (totalSeconds) => {
        const safeSeconds = Math.max(0, Number(totalSeconds || 0));
        const hours = String(Math.floor(safeSeconds / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((safeSeconds % 3600) / 60)).padStart(2, '0');
        const seconds = String(safeSeconds % 60).padStart(2, '0');

        return `${hours}:${minutes}:${seconds}`;
    };

    const sessionCard = document.querySelector('[data-session-started-at]');
    const sessionElapsedElement = document.getElementById('cashier-session-elapsed');
    const sessionStartedAt = sessionCard?.dataset.sessionStartedAt ? new Date(sessionCard.dataset.sessionStartedAt) : null;

    const renderSessionDuration = () => {
        if (!sessionStartedAt || Number.isNaN(sessionStartedAt.getTime())) {
            return;
        }

        const elapsedSeconds = Math.floor((Date.now() - sessionStartedAt.getTime()) / 1000);
        sessionElapsedElement.textContent = `${formatDuration(elapsedSeconds)} sejak login`;
    };

    if (sessionStartedAt && sessionElapsedElement) {
        renderSessionDuration();
        setInterval(renderSessionDuration, 1000);
    }

    const state = {
        selectedProduct: null,
        quantityInput: '',
        cart: [],
        paymentMethod: '',
        invoiceNumber: `INV-${new Date().toISOString().slice(0, 10).replaceAll('-', '')}-${Math.floor(Math.random() * 9000 + 1000)}`,
    };

    const quantityDisplay = document.getElementById('quantity-display');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const stockWarning = document.getElementById('stock-warning');
    const confirmOrderButton = document.getElementById('confirm-order');
    const receiptPanel = document.getElementById('receipt-panel');
    const receiptPaymentMethod = document.getElementById('receipt-payment-method');
    const receiptPaymentNumber = document.getElementById('receipt-payment-number');
    const receiptBody = document.getElementById('receipt-body');
    const paymentHint = document.getElementById('payment-hint');
    const receiptInvoice = document.getElementById('receipt-invoice');
    const receiptItems = document.getElementById('receipt-items');
    const receiptTotalItem = document.getElementById('receipt-total-item');
    const receiptGrandTotal = document.getElementById('receipt-grand-total');
    const printReceiptButton = document.getElementById('print-receipt');
    const paymentButtons = document.querySelectorAll('.payment-method');
    const csrfToken = @json(csrf_token());
    const paymentMethodDetails = @json($paymentMethods->keyBy('code')->map(fn ($paymentMethod) => [
        'label' => $paymentMethod->label,
        'account_number' => $paymentMethod->account_number,
    ]));

    const normalizeQuantityInput = (value) => value.replace(/\D/g, '').replace(/^0+/, '');

    const getQuantity = () => {
        const parsedQuantity = parseInt(state.quantityInput || '1', 10);
        return Number.isNaN(parsedQuantity) || parsedQuantity < 1 ? 1 : parsedQuantity;
    };

    const getCartTotals = () => {
        const totalItem = state.cart.reduce((sum, item) => sum + item.quantity, 0);
        const grandTotal = state.cart.reduce((sum, item) => sum + item.quantity * item.price, 0);

        return { totalItem, grandTotal };
    };

    const buildReceiptHtml = () => {
        const { totalItem, grandTotal } = getCartTotals();
        const paymentLabel = paymentMethodDetails[state.paymentMethod]?.label ?? '-';
        const paymentAccountNumber = paymentMethodDetails[state.paymentMethod]?.account_number ?? '';

        return `
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>${state.invoiceNumber}</title>
                <style>
                    body {
                        margin: 0;
                        padding: 24px;
                        font-family: Arial, sans-serif;
                        background: #fff;
                        color: #111827;
                    }
                    .receipt {
                        width: 320px;
                        margin: 0 auto;
                        font-size: 12px;
                        line-height: 1.5;
                    }
                    .title {
                        text-align: center;
                        margin-bottom: 16px;
                    }
                    .title h1 {
                        margin: 0;
                        font-size: 18px;
                    }
                    .muted {
                        color: #6b7280;
                    }
                    .divider {
                        border-top: 1px dashed #d1d5db;
                        margin: 12px 0;
                    }
                    .row {
                        display: flex;
                        justify-content: space-between;
                        gap: 12px;
                    }
                    .items {
                        margin-top: 8px;
                    }
                    .item {
                        margin-bottom: 10px;
                    }
                    .item strong {
                        display: block;
                        margin-bottom: 2px;
                    }
                    .total {
                        font-size: 13px;
                        font-weight: 700;
                    }
                    @media print {
                        body { padding: 0; }
                    }
                </style>
            </head>
            <body>
                <div class="receipt">
                    <div class="title">
                        <h1>KyoraPOS</h1>
                        <div class="muted">Struk Penjualan</div>
                    </div>
                    <div class="row"><span>Invoice</span><span>${state.invoiceNumber}</span></div>
                    <div class="row"><span>Kasir</span><span>Budi Pratama</span></div>
                    <div class="row"><span>Bayar</span><span>${paymentLabel}</span></div>
                    ${paymentAccountNumber ? `<div class="row"><span>Nomor</span><span>${paymentAccountNumber}</span></div>` : ''}
                    <div class="row"><span>Tanggal</span><span>${new Date().toLocaleString('id-ID')}</span></div>
                    <div class="divider"></div>
                    <div class="items">
                        ${state.cart.map((item) => `
                            <div class="item">
                                <strong>${item.name}</strong>
                                <div class="row muted">
                                    <span>${item.quantity} x ${formatCurrency(item.price)}</span>
                                    <span>${formatCurrency(item.quantity * item.price)}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="divider"></div>
                    <div class="row total"><span>Total Item</span><span>${totalItem}</span></div>
                    <div class="row total"><span>Grand Total</span><span>Rp ${formatCurrency(grandTotal)}</span></div>
                </div>
            </body>
            </html>
        `;
    };

    const updateReceiptPanel = () => {
        const { totalItem, grandTotal } = getCartTotals();

        if (!state.cart.length) {
            receiptPanel.classList.add('hidden');
            return;
        }

        receiptPanel.classList.remove('hidden');
        receiptInvoice.textContent = state.invoiceNumber;
        receiptPaymentMethod.textContent = paymentMethodDetails[state.paymentMethod]?.label ?? '-';
        const paymentAccountNumber = paymentMethodDetails[state.paymentMethod]?.account_number ?? '';
        receiptPaymentNumber.textContent = paymentAccountNumber ? `Nomor: ${paymentAccountNumber}` : '';
        receiptPaymentNumber.classList.toggle('hidden', !paymentAccountNumber);
        receiptTotalItem.textContent = String(totalItem);
        receiptGrandTotal.textContent = `Rp ${formatCurrency(grandTotal)}`;
        receiptBody.classList.toggle('hidden', !state.paymentMethod);
        paymentHint.classList.toggle('hidden', Boolean(state.paymentMethod));
        printReceiptButton.disabled = !state.paymentMethod;

        receiptItems.innerHTML = state.cart.map((item) => `
            <div class="rounded-2xl border border-white/10 bg-slate-950/35 p-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-white">${item.name}</p>
                        <p class="mt-1 text-xs text-slate-400">${item.sku}</p>
                    </div>
                    <button type="button" class="text-xs text-rose-300 hover:text-rose-200" data-remove="${item.id}">Hapus</button>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm text-slate-300">
                    <span>${item.quantity} x Rp ${formatCurrency(item.price)}</span>
                    <span class="font-semibold text-emerald-300">Rp ${formatCurrency(item.quantity * item.price)}</span>
                </div>
            </div>
        `).join('');
    };

    const render = () => {
        const quantity = getQuantity();
        quantityDisplay.textContent = state.quantityInput || '1';
        subtotalDisplay.textContent = state.selectedProduct
            ? `Rp ${formatCurrency(state.selectedProduct.price * quantity)}`
            : 'Rp 0';

        const stockExceeded = state.selectedProduct ? quantity > state.selectedProduct.stock : false;
        stockWarning.classList.toggle('hidden', !stockExceeded);
        confirmOrderButton.disabled = !state.selectedProduct || stockExceeded || state.selectedProduct.stock < 1;
        paymentButtons.forEach((button) => {
            button.classList.toggle('bg-cyan-400/15', button.dataset.paymentMethod === state.paymentMethod);
            button.classList.toggle('text-cyan-100', button.dataset.paymentMethod === state.paymentMethod);
            button.classList.toggle('border-cyan-300/30', button.dataset.paymentMethod === state.paymentMethod);
        });
        updateReceiptPanel();
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

    document.querySelectorAll('.payment-method').forEach((button) => {
        button.addEventListener('click', () => {
            state.paymentMethod = button.dataset.paymentMethod || '';
            render();
        });
    });

    confirmOrderButton.addEventListener('click', () => {
        if (!state.selectedProduct) {
            return;
        }

        const quantity = getQuantity();
        if (quantity > state.selectedProduct.stock) {
            render();
            return;
        }

        fetch(@json(route('kasir.confirm-item')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                product_id: state.selectedProduct.id,
                quantity,
            }),
        })
            .then(async (response) => {
                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Stok tidak mencukupi.');
                }

                return response.json();
            })
            .then((data) => {
                const existing = state.cart.find((item) => item.id === state.selectedProduct.id);

                if (existing) {
                    existing.quantity += quantity;
                } else {
                    state.cart.push({
                        ...state.selectedProduct,
                        quantity,
                    });
                }

                state.paymentMethod = '';

                const updatedStock = data.remaining_stock;
                const productCard = document.querySelector(`[data-product-id="${state.selectedProduct.id}"]`);
                const stockDisplay = document.querySelector(`[data-stock-display="${state.selectedProduct.id}"]`);

                if (productCard) {
                    const updatedProduct = {
                        ...state.selectedProduct,
                        stock: updatedStock,
                    };

                    productCard.dataset.product = JSON.stringify(updatedProduct);
                }

                if (stockDisplay) {
                    stockDisplay.textContent = String(updatedStock);
                }

                state.selectedProduct = null;
                state.quantityInput = '';
                render();
            })
            .catch((error) => {
                alert(error.message);
            });
    });

    document.getElementById('clear-product').addEventListener('click', () => {
        state.selectedProduct = null;
        state.quantityInput = '';
        state.paymentMethod = '';
        render();
    });

    receiptItems.addEventListener('click', (event) => {
        const target = event.target.closest('button');

        if (!target || !target.dataset.remove) {
            return;
        }

        state.cart = state.cart.filter((item) => String(item.id) !== target.dataset.remove);
        state.paymentMethod = '';
        render();
    });

    printReceiptButton.addEventListener('click', () => {
        if (!state.cart.length || !state.paymentMethod) {
            return;
        }

        const printWindow = window.open('', '_blank', 'width=420,height=720');
        if (!printWindow) {
            return;
        }

        printWindow.document.open();
        printWindow.document.write(buildReceiptHtml());
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
        }, 250);
    });

    render();
</script>
@endpush

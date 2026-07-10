<?php

namespace App\Http\Controllers;

use App\Domains\Account\Models\CashierSession;
use App\Domains\Product\Models\Category;
use App\Domains\Product\Models\Product;
use App\Domains\Transaction\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KasirDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $cashier = $request->user();
        $activeSession = null;

        if ($cashier?->role === 'cashier') {
            $activeSession = CashierSession::query()
                ->where('user_id', $cashier->id)
                ->whereNull('ended_at')
                ->latest('started_at')
                ->first();
        }

        $statistics = [
            'kategori' => Category::count(),
            'produk_aktif' => Product::where('is_active', true)->count(),
            'stok_rendah' => Product::where('is_active', true)->where('stock', '<=', 10)->count(),
            'transaksi_hari_ini' => Transaction::whereDate('created_at', today())->count(),
        ];

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'ilike', "%{$search}%")
                        ->orWhere('sku', 'ilike', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'ilike', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->limit(24)
            ->get()
            ->map(function (Product $product) {
                return [
                    
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'price' => (float) $product->selling_price,
                    'category' => $product->category?->name ?? '-',
                ];
            });

        return view('kasir.dashboard', [
            'statistics' => $statistics,
            'products' => $products,
            'search' => $search,
            'cashier' => $cashier,
            'activeCashierSession' => $activeSession,
            'cashierElapsedSeconds' => $activeSession?->started_at ? $activeSession->started_at->diffInSeconds(Carbon::now()) : null,
        ]);
    }
}

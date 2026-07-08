<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Category;
use App\Domains\Product\Models\Product;
use App\Domains\Transaction\Models\Transaction;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $statistics = [
            'kategori' => Category::count(),
            'produk' => Product::count(),
            'produk_nonaktif' => Product::where('is_active', false)->count(),
            'transaksi_total' => Transaction::count(),
        ];

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $lowStockProducts = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $recentProducts = Product::query()
            ->with('category')
            ->latest('id')
            ->limit(5)
            ->get();

        $recentTransactions = Transaction::query()
            ->with(['user', 'details.product'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'categories' => $categories,
            'lowStockProducts' => $lowStockProducts,
            'recentProducts' => $recentProducts,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Domains\Product\Models\Category;
use App\Domains\Product\Models\Product;
use App\Domains\Transaction\Models\TransactionDetail;
use App\Domains\Transaction\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::now();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $statistics = [
            'kategori' => Category::count(),
            'produk' => Product::count(),
            'produk_nonaktif' => Product::where('is_active', false)->count(),
            'transaksi_total' => Transaction::count(),
        ];

        $dailyTransactions = Transaction::query()
            ->where('is_voided', false)
            ->whereBetween('created_at', [$todayStart, $todayEnd]);

        $monthlyTransactions = Transaction::query()
            ->where('is_voided', false)
            ->whereBetween('created_at', [$monthStart, $monthEnd]);

        $reports = [
            'daily' => $this->summarizeTransactions($dailyTransactions),
            'monthly' => $this->summarizeTransactions($monthlyTransactions),
        ];

        $bestSellers = TransactionDetail::query()
            ->selectRaw('transaction_details.product_id, products.sku, products.name, categories.name as category_name, SUM(transaction_details.quantity) as sold_quantity, SUM(transaction_details.subtotal) as revenue, SUM((transaction_details.selling_price_at_transaction - transaction_details.cost_price_at_transaction) * transaction_details.quantity) as profit')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->leftJoin('products', 'products.id', '=', 'transaction_details.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('transactions.is_voided', false)
            ->whereNotNull('transaction_details.product_id')
            ->groupBy('transaction_details.product_id', 'products.sku', 'products.name', 'categories.name')
            ->orderByDesc('sold_quantity')
            ->limit(5)
            ->get();

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
            ->with(['user', 'voidedBy', 'details.product'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'reports' => $reports,
            'bestSellers' => $bestSellers,
            'categories' => $categories,
            'lowStockProducts' => $lowStockProducts,
            'recentProducts' => $recentProducts,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    /**
     * @return array{transactions:int,revenue:float,profit:float}
     */
    private function summarizeTransactions(Builder $query): array
    {
        $revenue = (float) (clone $query)->sum('grand_total');
        $cost = (float) (clone $query)->sum('total_cost');

        return [
            'transactions' => (int) (clone $query)->count(),
            'revenue' => $revenue,
            'profit' => $revenue - $cost,
        ];
    }
}

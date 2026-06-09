<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer les informations de l'entreprise
        $company = Company::instance();
        
        // Statistiques principales
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => Customer::count(),
            'total_products' => Product::where('is_active', true)->count(),
            'low_stock_products' => Product::where('track_stock', true)
                ->whereRaw('stock_quantity <= stock_alert_threshold')
                ->count(),
            'out_of_stock_products' => Product::where('track_stock', true)
                ->where('stock_quantity', 0)
                ->count(),
            'monthly_revenue' => Order::where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
        ];

        // Commandes récentes
        $recent_orders = Order::with('customer')
            ->latest()
            ->limit(10)
            ->get();

        // Meilleurs produits (top 5)
        $top_products = Product::select('products.id', 'products.name', 'products.price')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Ventes par mois (12 derniers mois)
        $monthly_sales = Order::where('status', 'delivered')
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Catégories avec le plus de ventes
        $top_categories = Category::select('categories.id', 'categories.name')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'company',
            'stats',
            'recent_orders',
            'top_products',
            'monthly_sales',
            'top_categories'
        ));
    }
}
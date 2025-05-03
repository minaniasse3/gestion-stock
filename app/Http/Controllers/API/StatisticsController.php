<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Afficher un résumé des statistiques
     */
    public function index()
    {
        $totalProducts = Product::count();
        $totalSuppliers = Supplier::count();
        $lowStockProducts = Product::where('quantity', '<=', DB::raw('alert_threshold'))->count();

        return response()->json([
            'totalProducts' => $totalProducts,
            'totalSuppliers' => $totalSuppliers,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    /**
     * Statistiques des produits
     */
    public function productsStats()
    {
        $totalValue = Product::sum(DB::raw('price * quantity'));
        $averagePrice = Product::avg('price');
        $topCategories = Product::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'totalValue' => $totalValue,
            'averagePrice' => $averagePrice,
            'topCategories' => $topCategories,
        ]);
    }

    /**
     * Statistiques des fournisseurs
     */
    public function suppliersStats()
    {
        $suppliers = Supplier::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get();

        $totalValue = DB::table('suppliers')
            ->join('products', 'suppliers.id', '=', 'products.supplier_id')
            ->select('suppliers.id', 'suppliers.name', DB::raw('SUM(products.price * products.quantity) as total_value'))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('total_value', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'suppliersByProductCount' => $suppliers,
            'suppliersByStockValue' => $totalValue,
        ]);
    }

    /**
     * Évolution du stock
     */
    public function stockEvolution(Request $request)
    {
        $period = $request->get('period', 'month');
        $limit = $request->get('limit', 6);

        if ($period === 'month') {
            $stockData = StockMovement::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit($limit)
            ->get();

            // Formater les données pour le front-end
            $labels = [];
            $values = [];
            foreach ($stockData as $data) {
                $date = \Carbon\Carbon::createFromDate($data->year, $data->month, 1);
                $labels[] = $date->translatedFormat('F Y');
                $values[] = $data->total_quantity;
            }
        } else {
            // Données quotidiennes, hebdomadaires, etc.
            // À implémenter selon les besoins
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    /**
     * Distribution des catégories
     */
    public function categoriesDistribution()
    {
        $categories = Product::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        $labels = $categories->pluck('category')->toArray();
        $values = $categories->pluck('count')->toArray();

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    /**
     * Top produits
     */
    public function topProducts(Request $request)
    {
        $type = $request->get('type', 'quantity');
        $limit = $request->get('limit', 5);

        if ($type === 'quantity') {
            $products = Product::orderBy('quantity', 'desc')
                ->limit($limit)
                ->get(['id', 'name', 'quantity', 'price']);
        } elseif ($type === 'value') {
            $products = Product::select('id', 'name', 'quantity', 'price', DB::raw('price * quantity as total_value'))
                ->orderBy('total_value', 'desc')
                ->limit($limit)
                ->get();
        } elseif ($type === 'movement') {
            // Produits avec le plus de mouvements récents
            $products = StockMovement::select('product_id', DB::raw('SUM(ABS(quantity)) as total_movement'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('product_id')
                ->orderBy('total_movement', 'desc')
                ->limit($limit)
                ->with('product:id,name,quantity,price')
                ->get()
                ->map(function ($movement) {
                    return [
                        'id' => $movement->product->id,
                        'name' => $movement->product->name,
                        'quantity' => $movement->product->quantity,
                        'price' => $movement->product->price,
                        'total_movement' => $movement->total_movement,
                    ];
                });
        }

        return response()->json($products);
    }
} 
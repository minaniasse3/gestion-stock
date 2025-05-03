<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Obtenir les statistiques de base pour le tableau de bord
     */
    public function getStats()
    {
        $totalProducts = Product::count();
        $totalSuppliers = Supplier::count();
        $totalStock = Product::sum('quantity');
        $totalStockValue = Product::sum(DB::raw('price * quantity'));
        $lowStockProducts = Product::where('quantity', '<=', DB::raw('alert_threshold'))->count();

        // Statistiques additionnelles comme les mouvements récents
        $recentMovements = StockMovement::count();
        $incomingStock = StockMovement::where('type', 'entrée')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantity');
        $outgoingStock = StockMovement::where('type', 'sortie')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantity');

        return response()->json([
            'totalProducts' => $totalProducts,
            'totalSuppliers' => $totalSuppliers,
            'totalStock' => $totalStock,
            'totalStockValue' => $totalStockValue,
            'lowStockProducts' => $lowStockProducts,
            'recentMovements' => $recentMovements,
            'incomingStock' => $incomingStock,
            'outgoingStock' => $outgoingStock
        ]);
    }

    /**
     * Obtenir les activités récentes
     */
    public function getRecentActivities()
    {
        $activities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'user' => $activity->user ? $activity->user->name : 'Système',
                    'date' => $activity->created_at->format('d/m/Y H:i')
                ];
            });

        return response()->json($activities);
    }

    /**
     * Obtenir les produits en stock critique
     */
    public function getCriticalStock()
    {
        $products = Product::where('quantity', '<=', DB::raw('alert_threshold'))
            ->with('supplier')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                    'alert_threshold' => $product->alert_threshold,
                    'supplier' => $product->supplier ? $product->supplier->name : 'Non défini',
                    'price' => $product->price,
                    'last_restock' => $this->getLastRestockDate($product->id)
                ];
            });

        return response()->json($products);
    }

    /**
     * Obtenir la date de dernière réapprovisionnement d'un produit
     */
    private function getLastRestockDate($productId)
    {
        $lastRestock = StockMovement::where('product_id', $productId)
            ->where('type', 'entrée')
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastRestock ? $lastRestock->created_at->format('d/m/Y') : null;
    }
} 
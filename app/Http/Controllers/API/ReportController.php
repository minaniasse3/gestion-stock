<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Excel;
use App\Exports\ProductsExport;
use App\Exports\SuppliersExport;
use App\Exports\StockMovementsExport;

class ReportController extends Controller
{
    /**
     * Afficher une liste des rapports.
     */
    public function index(Request $request)
    {
        $query = Report::query();

        // Filtrer par type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $reports = $query->paginate(10);

        return response()->json($reports);
    }

    /**
     * Générer un nouveau rapport.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:inventory,sales,suppliers,movements,low_stock',
            'format' => 'required|string|in:pdf,excel,csv',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
        ]);

        // Créer l'enregistrement du rapport
        $report = Report::create([
            'name' => $request->name,
            'type' => $request->type,
            'format' => $request->format,
            'period_start' => $request->period_start ?? null,
            'period_end' => $request->period_end ?? null,
            'created_by' => auth()->id(),
            'status' => 'en_cours',
        ]);

        // Générer le fichier de rapport
        $filePath = null;

        try {
            switch ($request->type) {
                case 'inventory':
                    $filePath = $this->generateInventoryReport($report);
                    break;
                case 'sales':
                    $filePath = $this->generateSalesReport($report);
                    break;
                case 'suppliers':
                    $filePath = $this->generateSuppliersReport($report);
                    break;
                case 'movements':
                    $filePath = $this->generateMovementsReport($report);
                    break;
                case 'low_stock':
                    $filePath = $this->generateLowStockReport($report);
                    break;
            }

            // Mettre à jour le rapport avec le chemin du fichier et marquer comme terminé
            $report->update([
                'file_path' => $filePath,
                'status' => 'terminé',
            ]);

            return response()->json([
                'report' => $report,
                'message' => 'Rapport généré avec succès'
            ], 201);
        } catch (\Exception $e) {
            // En cas d'erreur, mettre à jour le statut du rapport
            $report->update([
                'status' => 'échoué',
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Échec de la génération du rapport: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer un rapport d'inventaire
     */
    private function generateInventoryReport(Report $report)
    {
        $products = Product::with('supplier')->get();
        
        $fileName = 'inventory_' . time();
        $filePath = 'reports/' . $fileName . '.' . $report->format;
        
        if ($report->format === 'pdf') {
            $pdf = PDF::loadView('reports.inventory', [
                'products' => $products,
                'report' => $report,
                'date' => Carbon::now(),
            ]);
            
            Storage::put('public/' . $filePath, $pdf->output());
        } elseif ($report->format === 'excel' || $report->format === 'csv') {
            Excel::store(new ProductsExport($products), 'public/' . $filePath);
        }
        
        return $filePath;
    }

    /**
     * Générer un rapport de ventes
     */
    private function generateSalesReport(Report $report)
    {
        $query = StockMovement::where('type', 'vente')
            ->with('product');
            
        if ($report->period_start) {
            $query->where('created_at', '>=', $report->period_start);
        }
        
        if ($report->period_end) {
            $query->where('created_at', '<=', $report->period_end);
        }
        
        $sales = $query->get();
        
        $fileName = 'sales_' . time();
        $filePath = 'reports/' . $fileName . '.' . $report->format;
        
        // Logique similaire pour générer le fichier selon le format
        // ... implémentation similaire au rapport d'inventaire
        
        return $filePath;
    }

    /**
     * Générer un rapport de fournisseurs
     */
    private function generateSuppliersReport(Report $report)
    {
        $suppliers = Supplier::withCount('products')->get();
        
        $fileName = 'suppliers_' . time();
        $filePath = 'reports/' . $fileName . '.' . $report->format;
        
        // Logique similaire pour générer le fichier selon le format
        
        return $filePath;
    }

    /**
     * Générer un rapport de mouvements de stock
     */
    private function generateMovementsReport(Report $report)
    {
        $query = StockMovement::with('product');
            
        if ($report->period_start) {
            $query->where('created_at', '>=', $report->period_start);
        }
        
        if ($report->period_end) {
            $query->where('created_at', '<=', $report->period_end);
        }
        
        $movements = $query->get();
        
        $fileName = 'movements_' . time();
        $filePath = 'reports/' . $fileName . '.' . $report->format;
        
        // Logique similaire pour générer le fichier selon le format
        
        return $filePath;
    }

    /**
     * Générer un rapport de produits à faible stock
     */
    private function generateLowStockReport(Report $report)
    {
        $products = Product::where('quantity', '<=', DB::raw('alert_threshold'))
            ->with('supplier')
            ->get();
        
        $fileName = 'low_stock_' . time();
        $filePath = 'reports/' . $fileName . '.' . $report->format;
        
        // Logique similaire pour générer le fichier selon le format
        
        return $filePath;
    }

    /**
     * Afficher un rapport spécifique.
     */
    public function show(Report $report)
    {
        return response()->json($report);
    }

    /**
     * Supprimer un rapport spécifique.
     */
    public function destroy(Report $report)
    {
        // Supprimer le fichier associé s'il existe
        if ($report->file_path && Storage::exists('public/' . $report->file_path)) {
            Storage::delete('public/' . $report->file_path);
        }

        $report->delete();

        return response()->json([
            'message' => 'Rapport supprimé avec succès'
        ]);
    }

    /**
     * Télécharger un rapport
     */
    public function download(Report $report)
    {
        if (!$report->file_path || !Storage::exists('public/' . $report->file_path)) {
            return response()->json([
                'error' => 'Le fichier de rapport n\'existe pas'
            ], 404);
        }

        return Storage::download('public/' . $report->file_path, $report->name . '.' . $report->format);
    }
} 
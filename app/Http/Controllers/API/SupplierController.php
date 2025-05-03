<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Afficher une liste des fournisseurs.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Recherche par nom ou contact
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                 ->orWhere('contact_person', 'like', '%' . $request->search . '%')
                 ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $suppliers = $query->withCount('products')->paginate(10);

        return response()->json($suppliers);
    }

    /**
     * Recherche de fournisseurs
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $suppliers = Supplier::where('name', 'like', "%{$query}%")
                         ->orWhere('contact_person', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%")
                         ->withCount('products')
                         ->limit(10)
                         ->get();

        return response()->json($suppliers);
    }

    /**
     * Stocker un nouveau fournisseur.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier = Supplier::create($request->all());

        return response()->json([
            'supplier' => $supplier,
            'message' => 'Fournisseur créé avec succès'
        ], 201);
    }

    /**
     * Afficher le fournisseur spécifié.
     */
    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Mettre à jour le fournisseur spécifié.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'contact_person' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier->update($request->all());

        return response()->json([
            'supplier' => $supplier,
            'message' => 'Fournisseur mis à jour avec succès'
        ]);
    }

    /**
     * Supprimer le fournisseur spécifié.
     */
    public function destroy(Supplier $supplier)
    {
        // Vérifier s'il y a des produits associés à ce fournisseur
        $productsCount = $supplier->products()->count();
        if ($productsCount > 0) {
            return response()->json([
                'error' => 'Ce fournisseur ne peut pas être supprimé car il a des produits associés.'
            ], 409);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Fournisseur supprimé avec succès'
        ]);
    }

    /**
     * Récupérer les produits d'un fournisseur
     */
    public function getProducts(Supplier $supplier)
    {
        $products = $supplier->products()->paginate(10);
        return response()->json($products);
    }
} 
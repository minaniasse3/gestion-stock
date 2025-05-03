<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\UserProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Routes pour l'utilisateur authentifié
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'updatePassword']);

    // Routes pour le tableau de bord
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/activities', [DashboardController::class, 'getRecentActivities']);
    Route::get('/dashboard/critical-stock', [DashboardController::class, 'getCriticalStock']);
    
    // Routes pour les produits
    Route::apiResource('products', ProductController::class);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock']);
    Route::get('/products/category/{category}', [ProductController::class, 'getByCategory']);
    Route::get('/products/supplier/{supplier}', [ProductController::class, 'getBySupplier']);
    
    // Routes pour les fournisseurs
    Route::apiResource('suppliers', SupplierController::class);
    Route::get('/suppliers/search', [SupplierController::class, 'search']);
    Route::get('/suppliers/{supplier}/products', [SupplierController::class, 'getProducts']);
    
    // Routes pour les statistiques
    Route::get('/statistics', [StatisticsController::class, 'index']);
    Route::get('/statistics/products', [StatisticsController::class, 'productsStats']);
    Route::get('/statistics/suppliers', [StatisticsController::class, 'suppliersStats']);
    Route::get('/statistics/stock-evolution', [StatisticsController::class, 'stockEvolution']);
    Route::get('/statistics/categories', [StatisticsController::class, 'categoriesDistribution']);
    Route::get('/statistics/top-products', [StatisticsController::class, 'topProducts']);
    
    // Routes pour les rapports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'generate']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::delete('/reports/{report}', [ReportController::class, 'destroy']);
    Route::get('/reports/{report}/download', [ReportController::class, 'download']);
    
    // Routes pour la gestion des utilisateurs (admin seulement)
    Route::middleware(['can:manage-users'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::put('/users/{user}/status', [UserController::class, 'updateStatus']);
        Route::put('/users/{user}/role', [UserController::class, 'updateRole']);
    });

    // Routes pour le profil utilisateur
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::put('/user/profile', [UserProfileController::class, 'update']);
    Route::put('/user/password', [UserProfileController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

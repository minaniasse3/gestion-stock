<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Toutes les routes non-API redirection vers Vue
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*$');

// API Routes pour l'authentification
Route::post('/api/auth/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;
        
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    return response()->json([
        'message' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
    ], 401);
});

Route::post('/api/auth/register', function (Request $request) {
    \Log::info('Données reçues:', $request->all());
    
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Définir un rôle par défaut
            'active' => true, // Définir actif par défaut
        ]);

        Auth::login($user);
        $token = $user->createToken('auth-token')->plainTextToken;
        
        \Log::info('Utilisateur créé avec succès:', ['user_id' => $user->id]);
        
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::warning('Erreur de validation:', ['errors' => $e->errors()]);
        return response()->json([
            'message' => 'Les données fournies ne sont pas valides.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Erreur d\'enregistrement:', ['message' => $e->getMessage()]);
        return response()->json([
            'message' => 'Une erreur est survenue lors de l\'inscription: ' . $e->getMessage()
        ], 500);
    }
});

Route::post('/api/auth/logout', function (Request $request) {
    if ($request->user()) {
        $request->user()->tokens()->delete();
    }
    
    auth()->logout();
    
    return response()->json([
        'message' => 'Déconnecté avec succès'
    ]);
})->middleware('auth:sanctum');

Route::get('/api/auth/user', function (Request $request) {
    return response()->json($request->user());
})->middleware('auth:sanctum');

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Routes pour les produits
    Route::get('/products', function () {
        return view('products.index');
    });
    
    Route::get('/products/create', function () {
        return view('products.create');
    });
    
    // Routes pour les statistiques
    Route::get('/statistics', function () {
        return view('statistics');
    });
    
    // Routes pour les utilisateurs
    Route::get('/users', function () {
        return view('users.index');
    });
    
    // Routes pour les fournisseurs
    Route::get('/suppliers', function () {
        return view('suppliers.index');
    });
    
    // Routes pour les rapports
    Route::get('/reports', function () {
        return view('reports.index');
    });
});

// Route de diagnostic CSRF
Route::get('/debug-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_status' => session()->isStarted() ? 'Started' : 'Not started',
        'cookie_exists' => isset($_COOKIE['XSRF-TOKEN']) ? 'Yes' : 'No',
        'cookie_value' => isset($_COOKIE['XSRF-TOKEN']) ? substr($_COOKIE['XSRF-TOKEN'], 0, 10) . '...' : 'N/A',
    ]);
});

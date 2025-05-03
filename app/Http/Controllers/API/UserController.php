<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $role = $request->input('role');
        
        $query = User::query();
        
        // Search by name or email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($role) {
            $query->where('role', $role);
        }
        
        // Order by name ascending by default
        $query->orderBy('name');
        
        return $query->paginate($perPage);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => $request->input('active', true),
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'Utilisateur créé avec succès'
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'active' => 'boolean',
        ]);
        
        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'active' => $request->input('active', $user->active),
        ]);
        
        // Update password if provided
        if ($request->filled('password') && $request->filled('password_confirmation')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
        
        return response()->json([
            'user' => $user,
            'message' => 'Utilisateur mis à jour avec succès'
        ]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Protect against deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte'
            ], 403);
        }
        
        $user->delete();
        
        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
    
    /**
     * Update the user's active status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'active' => 'required|boolean',
        ]);
        
        // Protect against deactivating yourself
        if ($user->id === auth()->id() && !$request->active) {
            return response()->json([
                'message' => 'Vous ne pouvez pas désactiver votre propre compte'
            ], 403);
        }
        
        $user->update([
            'active' => $request->active,
        ]);
        
        return response()->json([
            'user' => $user,
            'message' => $request->active 
                ? 'Utilisateur activé avec succès' 
                : 'Utilisateur désactivé avec succès'
        ]);
    }
    
    /**
     * Update the user's role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
        ]);
        
        // Protect against demoting yourself from admin
        if ($user->id === auth()->id() && auth()->user()->role === 'admin' && $request->role !== 'admin') {
            return response()->json([
                'message' => 'Vous ne pouvez pas réduire vos propres privilèges d\'administrateur'
            ], 403);
        }
        
        $user->update([
            'role' => $request->role,
        ]);
        
        return response()->json([
            'user' => $user,
            'message' => 'Rôle de l\'utilisateur mis à jour avec succès'
        ]);
    }
} 
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // List all users
    public function index(Request $request)
    {
        $users = User::orderBy('username')->get()->map(function ($user) {
            return [
                'id'         => $user->id,
                'username'   => $user->username,
                'email'      => $user->email,
                'role'       => $user->role,
                'is_active'  => $user->is_active,
                'created_at' => $user->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully.',
            'count'   => $users->count(),
            'data'    => $users,
        ], 200);
    }

    // Create a new user account
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role'     => 'required|in:Admin,User',
        ]);

        $user = User::create([
            'username'  => $validated['username'],
            'email'     => $validated['email'] ?? ($validated['username'] . '@stockflow.local'),
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data'    => [
                'id'       => $user->id,
                'username' => $user->username,
                'role'     => $user->role,
            ],
        ], 201);
    }

    // Get a single user by ID
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data'    => [
                'id'         => $user->id,
                'username'   => $user->username,
                'email'      => $user->email,
                'role'       => $user->role,
                'is_active'  => $user->is_active,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }

    // Update role, active status, or password
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deactivating their own account
        if ($request->has('is_active') && !$request->boolean('is_active') && $user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account.',
            ], 403);
        }

        $validated = $request->validate([
            'username'  => 'sometimes|required|string|unique:users,username,' . $id,
            'role'      => 'sometimes|required|in:Admin,User',
            'is_active' => 'sometimes|boolean',
            'password'  => 'sometimes|string|min:4',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['is_active']) && !$validated['is_active']) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data'    => [
                'id'        => $user->id,
                'username'  => $user->username,
                'role'      => $user->role,
                'is_active' => $user->is_active,
            ],
        ], 200);
    }

    // Deactivate user and revoke their tokens
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user->update(['is_active' => false]);
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deactivated successfully.',
        ], 200);
    }

    // Admin: reset any user's password directly
    public function resetPassword(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:4',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
            'data'    => [
                'id'       => $user->id,
                'username' => $user->username,
            ],
        ], 200);
    }
}

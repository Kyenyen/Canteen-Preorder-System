<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index()
    {
        try {
            $users = User::orderBy('username')->get();

            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|unique:users|max:255',
                'email' => 'required|email|unique:users|max:255',
                'role' => 'required|in:user,admin',
                'password' => 'required|string|min:6',
            ]);

            // Generate a 5-character user ID
            do {
                $userId = strtoupper(substr(str_replace('-', '', Str::uuid()), 0, 5));
            } while (User::where('user_id', $userId)->exists());

            $user = User::create([
                'user_id' => $userId,
                'username' => $validated['username'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'username' => [
                    'required',
                    'string',
                    Rule::unique('users')->ignore($user->user_id, 'user_id'),
                    'max:255'
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->user_id, 'user_id'),
                    'max:255'
                ],
                'role' => 'required|in:user,admin',
                'password' => 'nullable|string|min:6',
            ]);

            $user->username = $validated['username'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $username = $user->username;

            $user->delete();

            return response()->json([
                'message' => "User '{$username}' deleted successfully"
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all orders for a specific user
     */
    public function getUserOrders($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $orders = Order::where('user_id', $userId)
                ->with(['payment' => function ($query) {
                    $query->select('payment_id', 'order_id', 'method', 'paid_at', 'refunded');
                }])
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'user' => $user,
                'orders' => $orders
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch user orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

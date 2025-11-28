<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:30',
            'email' => [
                'required',
                'string',
                'email',
                'max:30',
                'unique:users',
                'regex:/@(student|admin)\.tarc\.edu\.my$/i'
            ],
            'password' => 'required|string|min:6|confirmed',
        ]);

        $role = 'student';
        if (Str::endsWith($validated['email'], '@admin.tarc.edu.my')) {
            $role = 'admin';
        }

        $lastUser = User::orderBy('user_id', 'desc')->first();

        if ($lastUser) {
            $number = intval(substr($lastUser->user_id, 1)) + 1;
        } else {
            $number = 1;
        }

        $userId = 'U' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'user_id' => $userId,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->currentAccessToken();

        // FIX: Strictly check if the token is a PersonalAccessToken (database token).
        // If it is a TransientToken (cookie/session auth), this check fails, skipping the delete call.
        if ($accessToken instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $accessToken->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}

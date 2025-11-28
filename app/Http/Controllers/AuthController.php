<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset; 

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

    /**
     * Send a reset link email to the user.
     * Corresponds to the client posting to /api/forgot-password.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Laravel's Password::sendResetLink will handle token generation, storage,
        // and sending the Mailable (Notification).
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Success status message (e.g., "We have emailed your password reset link!")
            return response()->json(['message' => __($status)]);
        }

        // Failure status message (e.g., "We can't find a user with that email address.")
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * Reset the user's password.
     * Corresponds to the client posting to /api/reset-password after clicking the email link.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            // Note: Password minimum validation is often 8 characters for security,
            // but matching register's 6 is safer for consistency if register remains 6.
            'password' => 'required|string|min:6|confirmed', 
        ]);

        // Attempt to reset the password using the token
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update the user's password in the database
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Fire the PasswordReset event
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Success status message
            return response()->json(['message' => __($status)]);
        }

        // Failure status message (e.g., "This password reset token is invalid.")
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
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
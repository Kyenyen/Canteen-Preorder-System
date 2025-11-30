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
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // 1. Register with Custom Error Messages
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
        ], [
            'username.required' => 'Please enter a username.',
            'username.max' => 'Username cannot exceed 30 characters.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please enter a valid email format.',
            'email.unique' => 'This email is already registered.',
            'email.regex' => 'You must use a valid TARC email (@student.tarc.edu.my or @admin.tarc.edu.my).',
            'password.required' => 'A password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
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

    // 2. Login with Custom Messages
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email format.',
            'password.required' => 'Please enter your password.',
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

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Please enter a new password.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    // 3. Update Profile with Custom Messages
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'username' => 'required|string|max:30',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg', 
        ], [
            'username.required' => 'Username cannot be empty.',
            'username.max' => 'Username cannot exceed 30 characters.',
            'photo.image' => 'The uploaded file must be an image.',
            'photo.mimes' => 'Only jpeg, png and jpg formats are allowed.',
        ]);

        $user->username = $request->username;

        if ($request->hasFile('photo')) {
            // 1. Delete old photo if it exists in 'public/photos'
            // Using public_path() for direct file access
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            // 2. Store new photo in 'photos' folder
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Move directly to public folder (no symlink needed)
            $file->move(public_path('photos'), $filename);
            
            // 3. Save relative path
            $user->photo = 'photos/' . $filename; 
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user
        ]);
    }

    // 4. Change Password with Custom Messages
    public function changePassword(Request $request)
    {
        $user = $request->user();

        // Consolidated validation with messages
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'The new password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Check DB Match
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password you entered is incorrect.']
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->currentAccessToken();

        if ($accessToken instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $accessToken->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}
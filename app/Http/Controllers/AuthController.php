<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail; 
use App\Mail\OtpMail;

class AuthController extends Controller
{
    // 1. Register with Custom Error Messages
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                'unique:users', // Double check just in case
                'regex:/@(student\.tarc\.edu\.my|tarc\.edu\.my)$/i'
            ],
            'password' => 'required|string|min:6|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'username.required' => 'Please choose a username for your account.',
            'username.max' => 'Username cannot exceed 100 characters.',
            
            // Context-aware: If email is missing here, it means they didn't verify properly
            'email.required' => 'Please verify your TARC email address first.',
            'email.email' => 'The email format is invalid.',
            'email.unique' => 'Account already exists. Please go to Login.',
            'email.regex' => 'Registration is restricted to TARC emails only.',
            
            'password.required' => 'Please set a secure password.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            
            'g-recaptcha-response.required' => 'Please complete the Captcha verification.',
            'g-recaptcha-response.recaptcha' => 'Captcha verification failed.',
        ]);

        if (!$this->verifyCaptcha($request->input('g-recaptcha-response'))) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => ['Captcha verification failed. Please try again.'],
            ]);
        }

        $role = 'user';

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

    public function sendOtp(Request $request) {
        $request->validate(['email' => 'required|email']);

        $request->validate([
            'email' => [
                'required', 
                'email', 
                'unique:users', 
                'regex:/@(student\.tarc\.edu\.my|tarc\.edu\.my)$/i'
            ]
        ], [
            'email.required' => 'Please enter your student email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please log in instead.',
            'email.regex' => 'Registration is restricted to TARC emails only (@student.tarc.edu.my).',
        ]);
        
        $otp = rand(100000, 999999);
        
        Cache::put('otp_' . $request->email, $otp, 60);
        Mail::to($request->email)->send(new OtpMail($otp));
        
        return response()->json(['message' => 'OTP sent']);
    }

    public function verifyOtp(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ], [
            'otp.required' => 'Please enter the 6-digit code.',
            'otp.digits' => 'The verification code must be exactly 6 digits.'
        ]);

        $cachedOtp = Cache::get('otp_'.$request->email);
        
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }
        
        return response()->json(['message' => 'Verified']);
    }

    // 2. Login with Custom Messages
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'g-recaptcha-response' => 'required',

        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email format.',
            'password.required' => 'Please enter your password.',
            'g-recaptcha-response.required' => 'Please complete the Captcha verification.',
        ]);

        if (!$this->verifyCaptcha($request->input('g-recaptcha-response'))) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => ['Captcha verification failed.'],
            ]);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token, 
            'user' => $user
        ]);
    }

    private function verifyCaptcha($token)
    {
        if (app()->environment('testing')) {
            return true;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $token,
        ]);

        return $response->json('success') === true;
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
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $file->move(public_path('photos'), $filename);
            
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

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'The new password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

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
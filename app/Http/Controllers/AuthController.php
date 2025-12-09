<?php

namespace App\Http\Controllers;

use App\Mail\MessageMail;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $token = Str::random(64); // safe, URL-friendly random token

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verify_token' => $token,
        ]);

        $verifyUrl = url('/api/verify-email' . urlencode($token));

        Mail::to($user->email)->queue(new VerifyEmail($user, $verifyUrl));

        return response()->json([
            'message' => 'User Registration successful. Please check your email to verify your account.',
            'token' => $token,
        ]);
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verify_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        $user->email_verified_at = now();
        $user->email_verify_token = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Invalid credentials'
            ]);
        }

        if(!$user->email_verified_at){
            return response()->json([
                'message' => 'Email Verification is required!',
            ]);
        }

        $token = JWTAuth::attempt($request->only('email', 'password'));

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        // $user = Auth::user();
        // $user = auth()->user();

        if(!$user){
            return response()->json([
                'message' => 'User not logged in',
            ]);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if($request->has('name')){
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $user->update([
                'name' => $request->name,
            ]);
        }

        if($request->has('passworrd')){
            $request->validate([
                'password' => "required|string|min:6",
            ]);
            if(!Hash::check($request->old_password, $user->password)){
                return response()->json([
                    'message' => 'Your Current password does not match. Please Enter Correct password',
                ]);
            }
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully']);
    }
}

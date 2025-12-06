<?php

namespace App\Http\Controllers;

use App\Mail\MessageMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Mail::to($user->email)->queue(new MessageMail($user, 'Welcome to Our Platform', 'Thank you for registering!'));

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
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

        return response()->json(['user' => $user]);
    }

    // public function updateProfile(Request $request)
    // {
    //     $user = JWTAuth::parseToken()->authenticate();

    //     if ($request->has('name')) {
    //         $user->update(['name' => $request->name]);
    //     }
    //     if ($request->has('email')) {
    //         $user->update(['email' => $request->email]);
    //     }
    //     if ($request->has('password')) {
    //         $user->update(['password' => Hash::make($request->password)]);
    //     }

    //     return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    // }

    public function updateProfile(Request $request)
    {
        // return $request;
        // return response()->json(['message' => 'Not implemented'], 501);
        $request->validate([
            'password'     => 'required|min:6',
            'old_password' => 'required',
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        // dd($request->all());

        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
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

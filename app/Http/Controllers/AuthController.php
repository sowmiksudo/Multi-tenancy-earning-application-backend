<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // User auth controller

    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'phone' => 'required|digits:11',
            'password' => 'required|string|min:6',
        ]);

        // Attempt to log the user in
        if (auth()->attempt($request->only('phone', 'password'))) {
            // Generate a new token for the user
            $user = auth()->user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        }

        // If login fails, return an error response
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'email' => 'email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $tenant_id = Tenant::where('project_id', $request->header('X-Project-Id'))->value('id');
        if (!$tenant_id) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }
        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'tenant_id' => $tenant_id,
            'password' => bcrypt($request->password),
        ]);

        // Generate a new token for the user
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful, verify your phone number.',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        // Revoke the user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function sendOtp(Request $request)
    {
        // Validate the request data
        if(!$request->user()->id){
            return redirect()->route('login');
        }

        $user = $request->user();
        $otp = rand(100000, 999999); // Generate a random 6-digit OTP
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10); // Set OTP expiration time
        $user->save();



        return response()->json(['message' => 'OTP sent successfully'], 200);
    }
}

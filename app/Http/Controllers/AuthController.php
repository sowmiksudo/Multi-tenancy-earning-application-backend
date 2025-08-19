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

        $authenticated = auth()->attempt($request->only('phone', 'password'));
        // Attempt to log the user in
        if ($authenticated) {
            // Generate a new token for the user
            $user = auth()->user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ], 200);
        }

        // If login fails, return an error response
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
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
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
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
            'success' => true,
            'message' => 'Registration successful, verify your phone number.',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        // Revoke the user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ], 200);
    }

    public function initiateOtpVerification(Request $request)
    {
        // Validate the request data

        $user = auth()->user();
        if (!$user->id) {
            return redirect()->route('login');
        }
        $sent = $this->sendOtp($user);
        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP, contact support'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        // Validate the request data
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = $request->user();
        if ($user->otp === $request->otp && now()->lessThanOrEqualTo($user->otp_expires_at)) {
            $user->otp_verified_at = now();
            $user->otp = null; // Clear the OTP after verification
            $user->otp_expires_at = null; // Clear the expiration time
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP'
        ], 400);
    }

    public function reset_password(Request $request)
    {

        if ($request->isMethod('get')) {
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $sent = $this->sendOtp($user);
            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP, contact support'
                ], 500);
            }
        } else {

            // Validate the request data
            $request->validate([
                'phone' => 'required|digits:11',
                'new_password' => 'required|string|min:6',
                'otp' => 'required|digits:6',
            ]);

            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            if ($request->otp != $user->otp || now()->greaterThan($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 404);
            }

            $user->password = bcrypt($request->new_password);
            $user->otp_expires_at = null; // Clear the OTP expiration time
            $user->otp = null; // Clear the OTP after password reset
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successful'
            ], 200);
        }
    }

    public function logIp(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $ipAddress = $request->ip();
        $country = null;

        // Log the IP address
        $user->userIps()->updateOrCreate(
            ['ip_address' => $ipAddress],
            ['country' => $country]
        );

        return response()->json([
            'success' => true,
            'message' => 'IP address logged successfully'
        ], 200);
    }

    //// HELPER FUNCTIONS ////


    public function sendOtp($user)
    {

        $otp = rand(100000, 999999); // Generate a random 6-digit OTP
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10); // Set OTP expiration time
        $user->save();

        $sent = $this->sendSms($user->phone, "Your OTP is: $otp");
        return $sent;
    }

    public function sendSms($phone, $message)
    {
        // Implement SMS sending logic here
        return true;
    }
}

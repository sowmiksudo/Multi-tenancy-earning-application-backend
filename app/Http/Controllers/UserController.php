<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserProfile()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'detailed'=>$user->user_profiles()->get()
        ], 200);
    }

    public function update_user_profile(Request $request)
    {
        // Update the user's profile
        $user = auth()->user();
        $user->update($request->only('name', 'phone', 'email'));
        $user->save();
        if($request->detailed) {
            $user->user_profiles()->updateOrCreate(
                ['user_id' => $user->id],
                $request->detailed
            );
        }
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user,
            'detailed' => $user->user_profiles()->get(),
        ], 200);
    }
}

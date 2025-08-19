<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class InvalidActivityController extends Controller
{
    public function index(Request $request)
    {
        // Fetch invalid activities for the authenticated user
        $activities = $request->user()->invalidActivities()->get();
        return response()->json([
            "success"=> true,
            "total"=> $activities->count(),
            "data" => $activities,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'activity_type' => 'required|string',
            'description' => 'required|string',
        ]);

        $limit = Setting::where('key', 'invalid_activity_limit')->first()->value;
        if($limit > 0 && $request->user()->invalidActivities()->count() >= $limit) {
            auth()->user()->trashed();
            return response()->json([
                'success' => false,
                'message' => 'Invalid activity limit reached, account suspended.',
            ], 403);
        }
        // Create a new invalid activity
        $activity = $request->user()->invalidActivities()->create([
            'activity_type' => $request->activity_type,
            'description' => $request->description,
            'tenant_id' => $request->user()->tenant_id, // Assuming tenant_id is available in the user model
            'task_id' => $request->task_id,
        ]);

        return response()->json($activity, 201);
    }
}



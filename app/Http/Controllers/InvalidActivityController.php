<?php

namespace App\Http\Controllers;

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



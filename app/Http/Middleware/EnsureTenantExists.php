<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->header('X-Project-ID');

        if (!$projectId) {
            return response()->json(['error' => 'Project ID is required'], 400);
        }

        $tenant = Tenant::where('project_id', $projectId)->first();

        if (!$tenant) {
            return response()->json(['success'=>false, 'message' => 'Invalid Project ID'], 404);
        }

        if ($tenant->status !== 'active') {
            return response()->json(['success'=>false, 'message' => 'Project is not active'], 403);
        }

        if (auth()->user()->trashed()) {
            return response()->json(['success'=>false, 'message' => 'Your account has been suspended.'], 404);
        }

        // Store the tenant in the request for later use
        $request->merge(['tenant' => $tenant]);
        $request->tenant = $tenant;

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Applet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Task::where('applet_id', $request->route('applet_id'))
            ->get();
    }

    public function start_task(Request $request)
    {
        $task_id = $request->route('task');

        $task = Task::find($task_id);
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
        if ($task->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Task is not active'
            ], 400);
        }


        $range = Task::find($task_id)->limit_type == 'daily' ? Carbon::today() : Carbon::now()->subDays(30); // Example: last 30 days
        $task_history = auth()->user()->transactions()
            ->where('task_id', $task_id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->where('created_at', '>=', $range)
            ->get();

        if (count($task_history) >= Task::find($task_id)->task_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Task limit reached. Try again later.'
            ], 400);
        }

        // Logic to check if user has passed the break time between two tasks.
        $last_task = auth()->user()->transactions()
            ->where('task_id', $task_id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->latest()
            ->first();
        if ($last_task) {
            $break_time = Task::find($task_id)->break_time; // in minutes
            $next_available_time = Carbon::parse($last_task->created_at)->addMinutes($break_time);
            if (Carbon::now()->lt($next_available_time)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to wait before starting this task again.',
                    'next_available_time' => $next_available_time->toDateTimeString()
                ], 400);
            }
        }

        // Logic to start the task
        $task->status = 'active';
        $task->start_date = now();
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task started successfully',
            'task' => $task
        ], 200);
    }

    public function stop_task(Request $request)
    {
        $task_id = $request->route('task');
        $task = Task::find($task_id);
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
        if ($task->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Task is not active'
            ], 400);
        }


        $range = Task::find($task_id)->limit_type == 'daily' ? Carbon::today() : Carbon::now()->subDays(30); // Example: last 30 days
        $task_history = auth()->user()->transactions()
            ->where('task_id', $task_id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->where('created_at', '>=', $range)
            ->get();

        if (count($task_history) >= Task::find($task_id)->task_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Task limit reached. Try again later.'
            ], 400);
        }


        // Logic to stop the task
        $task->status = 'active';
        $task->end_date = now();
        $task->save();

        Transaction::create([
            'tenant_id' => auth()->user()->tenant_id,
            'applet_id' => $task->applet_id,
            'user_id' => auth()->user()->id,
            'task_id' => $task->id,
            'type' => 'credit',
            'category' => 'task_completion',
            'amount' => $task->reward,
            'description' => "Task {$task->name} completed",
            'status' => 'completed',
            'balance_before' => auth()->user()->wallet->balance,
            'balance_after' => auth()->user()->wallet->balance + $task->reward,
        ]);

        $user = auth()->user();
        $user->wallet->balance += $task->reward;
        $user->wallet->total_earned += $task->reward;
        $user->wallet->last_transaction_at = now();
        $user->wallet->save();

        return response()->json([
            'success' => true,
            'message' => 'Task completed successfully',
            'task_id' => $task->id,
            'remaining_tasks' => $task->task_limit - count($task_history) - 1,
        ], 200);
    }

    public function show(Request $request)
    {
        $task = Task::find($request->route('task'));
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'task' => $task
        ], 200);
    }
}

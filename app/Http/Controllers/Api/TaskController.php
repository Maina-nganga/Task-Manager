<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'due_date' => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'priority' => 'required|in:low,medium,high',
        ], [
            'title.required'          => 'A task title is required.',
            'title.string'            => 'The task title must be a string.',
            'title.max'               => 'The task title may not exceed 255 characters.',
            'due_date.required'       => 'A due date is required.',
            'due_date.date'           => 'The due date must be a valid date.',
            'due_date.date_format'    => 'The due date must be in YYYY-MM-DD format.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'priority.required'       => 'A priority is required.',
            'priority.in'             => 'Priority must be one of: low, medium, high.',
        ]);

        $duplicate = Task::where('title', $validated['title'])
                         ->where('due_date', $validated['due_date'])
                         ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'A task with this title already exists for the given due date.',
                'errors'  => [
                    'title' => ['The title has already been taken for this due date.'],
                ],
            ], 422);
        }

        $task = Task::create($validated);

        return response()->json([
            'message' => 'Task created successfully.',
            'data'    => $task,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'sometimes|in:pending,in_progress,done',
        ], [
            'status.in' => 'Status must be one of: pending, in_progress, done.',
        ]);

        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->get();

        $sorted = $tasks->sortBy([
            fn ($a, $b) => Task::PRIORITY_ORDER[$b->priority] <=> Task::PRIORITY_ORDER[$a->priority],
            fn ($a, $b) => $a->due_date <=> $b->due_date,
        ])->values();

        if ($sorted->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found' . ($request->filled('status') ? ' for status: ' . $request->status : '') . '.',
                'data'    => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'count'   => $sorted->count(),
            'data'    => $sorted,
        ], 200);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $newStatus = $task->nextAllowedStatus();

        if (! $newStatus) {
            return response()->json([
                'message' => 'Task is already completed (done). No further status changes are allowed.',
                'data'    => $task,
            ], 422);
        }

        $task->status = $newStatus;
        $task->save();

        return response()->json([
            'message' => 'Task status updated successfully.',
            'data'    => $task,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (! $task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        if (! $task->isDeletable()) {
            return response()->json([
                'message'        => 'Only completed (done) tasks can be deleted.',
                'current_status' => $task->status,
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ], 200);
    }

    public function dailyReport(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|date_format:Y-m-d',
        ], [
            'date.required'    => 'A date is required.',
            'date.date'        => 'The date must be a valid date.',
            'date.date_format' => 'The date must be in YYYY-MM-DD format.',
        ]);

        $date = $request->date;

        $rows = Task::select('priority', 'status', DB::raw('COUNT(*) as count'))
                    ->where('due_date', $date)
                    ->groupBy('priority', 'status')
                    ->get();

        $priorities = ['high', 'medium', 'low'];
        $statuses   = ['pending', 'in_progress', 'done'];

        $summary = [];
        foreach ($priorities as $priority) {
            foreach ($statuses as $status) {
                $summary[$priority][$status] = 0;
            }
        }

        foreach ($rows as $row) {
            $summary[$row->priority][$row->status] = (int) $row->count;
        }

        return response()->json([
            'date'    => $date,
            'summary' => $summary,
        ], 200);
    }
}
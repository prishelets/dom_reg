<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    public function get_tasks(Request $request)
    {
        $registrar = $request->query('registrar');
        $mode = $request->query('mode');
        $limit = (int) $request->query('limit', 1);

        $query = Task::query();

        if (!empty($registrar)) {
            $query->where('registrar', $registrar);
        }

        // MODE: Registration (регистрация домена)
        if ($mode === 'reg') {
            $query->where(function ($q) {
                $q->whereNull('email_login')
                  ->orWhere('email_login', '');
            });

            $query->where('completed', false);
        }

        $tasks = $query->orderBy('id', 'asc')
                       ->limit($limit)
                       ->get();

        return response()->json([
            'success' => true,
            'count' => $tasks->count(),
            'tasks' => $tasks,
        ]);
    }

    public function update(Request $request, $task_id)
    {
        $rules = [
            'status'      => 'sometimes|required|string|max:255',
            'email_login' => 'sometimes|required|string|max:255',
        ];

        $data = $request->validate($rules);

        $payload = [];
        foreach (array_keys($rules) as $field) {
            if ($request->has($field)) {
                $payload[$field] = $data[$field];
            }
        }

        if (empty($payload)) {
            return response()->json([
                'success' => false,
                'error'   => 'No valid fields provided for update',
            ], 422);
        }

        $task = Task::find($task_id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error'   => 'Task not found',
            ], 404);
        }

        $task->fill($payload);
        $task->save();

        return response()->json([
            'success' => true,
            'id'      => $task->id,
            'updated' => $payload,
        ]);
    }
}

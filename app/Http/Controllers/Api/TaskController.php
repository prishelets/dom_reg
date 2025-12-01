<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    public function get_tasks()
    {
        $tasks = Task::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }
}
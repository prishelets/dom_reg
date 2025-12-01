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
        'tasks' => $tasks
    ]);
}



}
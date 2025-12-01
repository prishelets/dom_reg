<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    public function get_tasks(Request $request)
    {
        // параметры из URL
        $registrar = $request->query('registrar');
        $mode = $request->query('mode');       // reg, cf, email, etc (на будущее)
        $limit = (int) $request->query('limit', 1); // по умолчанию 1

        // базовый запрос
        $query = Task::query();

        // фильтр по регистратору
        if (!empty($registrar)) {
            $query->where('registrar', $registrar);
        }

        // режим "reg" — регистрация домена
        if ($mode === 'reg') {
            $query->where(function ($q) {
                $q->whereNull('email_login')
                ->orWhere('email_login', '');
            });

            $query->where('completed', false);
        }

        // фильтр по лимиту
        $tasks = $query->orderBy('id', 'asc')->limit($limit)->get();

        return response()->json([
            'success' => true,
            'count' => $tasks->count(),
            'tasks' => $tasks
        ]);
    }


}
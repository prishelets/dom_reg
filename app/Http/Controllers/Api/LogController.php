<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->validate([
            'task_id'       => 'nullable|integer|exists:tasks,id',
            'template_name' => 'nullable|string|max:255',
            'type'          => 'nullable|string|max:50',
            'text'          => 'required|string',
            'error_id'      => 'nullable|string|max:255',
        ]);

        $log = Log::create([
            'task_id'       => $data['task_id'] ?? null,
            'template_name' => $data['template_name'] ?? null,
            'type'          => $data['type'] ?? null,
            'text'          => $data['text'],
            'error_id'      => $data['error_id'] ?? null,
            'created_at'    => now(),
        ]);

        return response()->json([
            'success' => true,
            'log_id'  => $log->id,
        ], 201);
    }
}

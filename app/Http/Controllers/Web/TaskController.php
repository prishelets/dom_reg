<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Log;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::withExists('logs')
            ->orderBy('id', 'desc')
            ->get();

        return view('tasks.list', [
            'tasks' => $tasks
        ]);
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        // Валидация
        $request->validate([
            'registrar' => 'required|string|max:255',
            'country'   => 'required|string|max:255',
            'brand'     => 'nullable|string|max:255',
            'domains'   => 'required|string',
        ]);

        // Преобразуем домены в массив
        $domains = preg_split('/\r\n|\r|\n/', trim($request->domains));
        $domains = array_filter($domains); // убираем пустые строки

        $created = 0;

        foreach ($domains as $domain) {

            Task::create([
                'registrar' => $request->registrar,
                'country'   => $request->country,
                'brand'     => $request->brand,
                'domain'    => trim($domain),
                'status'    => 'created',
                'completed' => false,
            ]);

            $created++;
        }

        return redirect('/tasks')->with('success', "Created {$created} tasks.");
    }

    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return redirect('/tasks')->with('error', 'Task not found');
        }

        $task->delete();

        return redirect('/tasks')->with('success', 'Task deleted successfully');
    }

    public function logs(Task $task)
    {
        $logs = Log::where('task_id', $task->id)
            ->orderBy('created_at', 'asc')
            ->get(['created_at', 'type', 'mode', 'text']);

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'logs' => $logs,
        ]);
    }
}

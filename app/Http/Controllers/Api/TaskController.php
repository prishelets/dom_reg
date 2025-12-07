<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function get_tasks(Request $request)
    {
        $registrar = $request->query('registrar');
        $mode = $request->query('mode');
        $scheme = $request->query('scheme');

        if ($scheme === 'dynadot_reg_domain' || $mode === 'reg_domain') {
            return $this->regDomainResponse($request);
        }

        if ($scheme === 'dynadot_account_reg') {
            $registrar = 'dynadot.com';
            $mode = 'reg';

            $enabled = (int) Setting::firstOrCreate(
                ['name' => 'dynadot_next_account_creation_enabled'],
                ['value' => 1]
            )->value;

            if ($enabled) {
                $nextRunValue = Setting::firstOrCreate(
                    ['name' => 'dynadot_next_account_creation_at'],
                    ['value' => now()->toDateTimeString()]
                )->value;

                $nextRun = Carbon::parse($nextRunValue);
                if (now()->lt($nextRun)) {
                    return response()->json([
                        'success' => true,
                        'count'   => 0,
                        'tasks'   => [],
                        'message' => 'Dynadot registration is scheduled at ' . $nextRun->toDateTimeString(),
                    ]);
                }
            }
        }
        $limit = (int) $request->query('limit', 10);

        $query = Task::query();

        if (!empty($registrar)) {
            $query->where('registrar', $registrar);
        }

        $useDefaultOrdering = true;

        // MODE: Registration (регистрация домена)
        if ($mode === 'reg') {
            $query->where(function ($q) {
                $q->whereNull('registrar_password')
                  ->orWhere('registrar_password', '');
            });

            $query->where('completed', false);
        } elseif ($mode === 'cf') {
            $query->where('completed', false)
                  ->where('domain_paid', true)
                  ->where(function ($q) {
                      $q->whereNull('cloudflare_email')
                        ->orWhere('cloudflare_email', '');
                  });

            $query->inRandomOrder();
            $useDefaultOrdering = false;
        }

        if ($mode === 'cf') {
            $query->select(['id', 'domain', 'registrar_email', 'email_password']);
        }

        if ($useDefaultOrdering) {
            $query->orderBy('id', 'asc');
        }

        $tasks = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'count' => $tasks->count(),
            'tasks' => $tasks,
        ]);
    }

    public function warmup(Request $request)
    {
        return $this->warmupResponse($request);
    }

    public function update(Request $request, $task_id)
    {
        if (!ctype_digit((string) $task_id)) {
            return response()->json([
                'success' => false,
                'error'   => 'Invalid task id',
            ], 400);
        }

        $task = Task::find($task_id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error'   => 'Task not found',
            ], 404);
        }

        $booleanFields = ['completed', 'domain_paid', 'ns_at_registrar'];
        $numericFields = ['domain_paid_price'];

        $payload = [];
        foreach ($task->getFillable() as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);
                if ($value === 'NOW()' || strtolower((string) $value) === 'now') {
                    $value = now()->toDateTimeString();
                }
                if (in_array($field, $booleanFields, true)) {
                    if (is_string($value)) {
                        $normalized = strtolower($value);
                        if (in_array($normalized, ['true', '1', 'yes'], true)) {
                            $value = true;
                        } elseif (in_array($normalized, ['false', '0', 'no'], true)) {
                            $value = false;
                        }
                    }
                    $value = (bool) $value;
                }
                if (in_array($field, $numericFields, true)) {
                    if (is_string($value)) {
                        $clean = preg_replace('/[^0-9.\-]/', '', $value);
                        $value = $clean === '' ? null : $clean;
                    }
                }
                $payload[$field] = $value;
            }
        }

        if (empty($payload)) {
            return response()->json([
                'success' => false,
                'error'   => 'No valid fields provided for update',
            ], 422);
        }

        $task->fill($payload);
        $task->save();

        return response()->json([
            'success' => true,
            'id'      => $task->id,
            'updated' => $payload,
        ]);
    }

    public function addAccount(Request $request, $task_id)
    {
        if (!ctype_digit((string) $task_id)) {
            return response()->json([
                'success' => false,
                'error'   => 'Invalid task id',
            ], 400);
        }

        $task = Task::find($task_id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error'   => 'Task not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'registrar_email'    => 'nullable|string|max:255|required_without:registrar_login',
            'registrar_login'    => 'nullable|string|max:255|required_without:registrar_email',
            'registrar_password' => 'required|string|max:255',
            'email_password'     => 'nullable|string|max:255',
            'first_name'         => 'nullable|string|max:255',
            'last_name'          => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'zip'                => 'nullable|string|max:50',
            'phone'              => 'nullable|string|max:100',
            'proxy'              => 'nullable|string|max:255',
            'security_qa'        => 'nullable|string',
            'status'             => 'nullable|string|max:255',
        ], [
            'registrar_email.required_without' => 'Either registrar email or login is required.',
            'registrar_login.required_without' => 'Either registrar email or login is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'server_time' => now()->toDateTimeString(),
            ], 422);
        }

        $data = $validator->validated();

        $fields = [
            'registrar_email',
            'registrar_login',
            'registrar_password',
            'email_password',
            'first_name',
            'last_name',
            'city',
            'address',
            'zip',
            'phone',
            'proxy',
            'security_qa',
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                $task->{$field} = $value === '' ? null : $value;
            }
        }

        $task->status = $data['status'] ?? 'registrar account created';
        $task->account_created_at = now();
        $task->account_ready_at = $this->calculateAccountReadyAt();
        $task->account_next_check_at = $this->calculateAccountNextCheckAt($task->account_ready_at);
        $task->save();

        $this->updateDynadotSchedule();

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'message' => 'Task updated successfully',
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    public function sendWarmupSuccess($task_id)
    {
        if (!ctype_digit((string) $task_id)) {
            return response()->json([
                'success' => false,
                'error'   => 'Invalid task id',
            ], 400);
        }

        $task = Task::find($task_id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error'   => 'Task not found',
            ], 404);
        }

        $task->account_next_check_at = $this->calculateAccountNextCheckAt($task->account_ready_at);
        $task->save();

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'next_check_at' => $task->account_next_check_at,
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    private function updateDynadotSchedule(): void
    {
        $from = (int) Setting::firstOrCreate(
            ['name' => 'dynadot_next_account_creation_interval_from'],
            ['value' => 5]
        )->value;

        $to = (int) Setting::firstOrCreate(
            ['name' => 'dynadot_next_account_creation_interval_to'],
            ['value' => 10]
        )->value;

        if ($to < $from) {
            $to = $from;
        }

        $minutes = random_int($from, $to);
        $nextRun = Carbon::now()->addMinutes($minutes)->toDateTimeString();

        Setting::updateOrCreate(
            ['name' => 'dynadot_next_account_creation_at'],
            ['value' => $nextRun]
        );
    }

    private function calculateAccountReadyAt(): string
    {
        $from = (int) Setting::firstOrCreate(
            ['name' => 'dynadot_account_ready_interval_from'],
            ['value' => 1]
        )->value;

        $to = (int) Setting::firstOrCreate(
            ['name' => 'dynadot_account_ready_interval_to'],
            ['value' => 2]
        )->value;

        if ($to < $from) {
            $to = $from;
        }

        $minSeconds = $from * 3600;
        $maxSeconds = $to * 3600;
        $seconds = random_int($minSeconds, max($minSeconds, $maxSeconds));

        return Carbon::now()->addSeconds($seconds)->toDateTimeString();
    }

    private function calculateAccountNextCheckAt(?string $readyAt = null): string
    {
        $from = (int) Setting::firstOrCreate(
            ['name' => 'dynadot_account_check_interval_from'],
            ['value' => 1]
        )->value;

        $to = (int) Setting::firstOrCreate(
            ['name' => 'dynadot_account_check_interval_to'],
            ['value' => 3]
        )->value;

        if ($to < $from) {
            $to = $from;
        }

        $minSeconds = $from * 3600;
        $maxSeconds = $to * 3600;
        $seconds = random_int($minSeconds, max($minSeconds, $maxSeconds));

        $next = Carbon::now()->addSeconds($seconds);

        if ($readyAt) {
            $ready = Carbon::parse($readyAt);
            if ($next->gte($ready)) {
                $next = $ready;
            }
        }

        return $next->toDateTimeString();
    }

    private function warmupResponse(Request $request)
    {
        $now = now();
        $limit = (int) $request->query('limit', 10);
        $sortInput = strtolower($request->query('sort_by', 'random'));
        $sortMode = match ($sortInput) {
            'asc', 'ascend', 'ascending' => 'asc',
            'desc', 'descend', 'descending' => 'desc',
            default => 'random',
        };

        $query = Task::query()
            ->where('registrar', 'dynadot.com')
            ->where(function ($subQuery) use ($now) {
                $subQuery->whereNull('account_ready_at')
                         ->orWhere('account_ready_at', '>', $now);
            })
            ->where(function ($subQuery) use ($now) {
                $subQuery->whereNull('account_next_check_at')
                         ->orWhere('account_next_check_at', '<=', $now);
            });

        $requiredFields = [
            'registrar_email',
            'registrar_login',
            'registrar_password',
            'email_password',
            'first_name',
            'last_name',
            'city',
            'address',
            'zip',
            'phone',
        ];

        foreach ($requiredFields as $field) {
            $query->whereNotNull($field)
                  ->where($field, '<>', '');
        }

        if ($sortMode === 'asc') {
            $query->orderByRaw('CASE WHEN account_next_check_at IS NULL THEN 0 ELSE 1 END')
                  ->orderBy('account_next_check_at', 'asc');
        } elseif ($sortMode === 'desc') {
            $query->orderByRaw('CASE WHEN account_next_check_at IS NULL THEN 1 ELSE 0 END')
                  ->orderBy('account_next_check_at', 'desc');
        } else {
            $query->inRandomOrder();
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $tasks = $query->get();

        return response()->json([
            'success' => true,
            'count'   => $tasks->count(),
            'tasks'   => $tasks,
        ]);
    }

    private function regDomainResponse(Request $request)
    {
        $now = now();
        $limit = (int) $request->query('limit', 1);
        $registrar = $request->query('registrar');
        $sortInput = strtolower($request->query('sort_by', 'random'));
        $sortMode = match ($sortInput) {
            'asc', 'ascend', 'ascending' => 'asc',
            'desc', 'descend', 'descending' => 'desc',
            default => 'random',
        };

        $query = Task::query()
            ->when(!empty($registrar), fn ($q) => $q->where('registrar', $registrar))
            ->where(function ($q) {
                $q->where('domain_paid', false)
                  ->orWhereNull('domain_paid');
            })
            ->where('completed', false)
            ->whereNotNull('account_ready_at')
            ->where('account_ready_at', '<=', $now);

        $requiredFields = [
            'registrar_email',
            'registrar_login',
            'registrar_password',
            'email_password',
            'first_name',
            'last_name',
            'city',
            'address',
            'zip',
            'phone',
        ];

        foreach ($requiredFields as $field) {
            $query->whereNotNull($field)
                  ->where($field, '<>', '');
        }

        if ($sortMode === 'asc') {
            $query->orderBy('account_ready_at', 'asc');
        } elseif ($sortMode === 'desc') {
            $query->orderBy('account_ready_at', 'desc');
        } else {
            $query->inRandomOrder();
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $tasks = $query->get();

        return response()->json([
            'success' => true,
            'count'   => $tasks->count(),
            'tasks'   => $tasks,
        ]);
    }
}

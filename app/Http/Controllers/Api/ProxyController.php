<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proxy;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProxyController extends Controller
{
    private bool $modeColumnChecked = false;
    private bool $modeColumnExists = false;

    public function get()
    {
        $proxy = null;

        DB::transaction(function () use (&$proxy) {
            Proxy::where('active', true)
                ->whereNotNull('last_used_at')
                ->where('last_used_at', '<', now()->subMinutes(20))
                ->update(['active' => false]);

            $baseQuery = Proxy::where('active', false)->lockForUpdate();

            $proxy = (clone $baseQuery)
                ->whereNull('last_used_at')
                ->inRandomOrder()
                ->first();

            if (!$proxy) {
                $proxy = (clone $baseQuery)
                    ->whereNotNull('last_used_at')
                    ->orderBy('last_used_at')
                    ->first();
            }

            if ($proxy) {
                $proxy->last_used_at = now();
                $proxy->active = true;
                $proxy->save();
            }
        }, 3);

        if (!$proxy) {
            return response()->json([
                'success' => false,
                'message' => 'No active proxies available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Proxy retrieved',
            'proxy' => [
                'id' => $proxy->id,
                'protocol' => $proxy->protocol,
                'login' => $proxy->login,
                'password' => $proxy->password,
                'ip' => $proxy->ip,
                'port' => $proxy->port,
                'last_used_at' => $proxy->last_used_at?->toDateTimeString(),
            ],
        ]);
    }

    public function sendSuccess(Request $request)
    {
        return $this->handleReport($request, 'success');
    }

    public function sendError(Request $request)
    {
        return $this->handleReport($request, 'error');
    }

    private function handleReport(Request $request, string $resultType)
    {
        try {
            $data = $request->validate([
                'proxy' => 'required|string',
                'mode'  => 'nullable|string|max:255',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $parsed = $this->parseProxyString($data['proxy']);

        if (!$parsed) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid proxy format',
            ], 422);
        }

        $proxy = Proxy::where('ip', $parsed['ip'])
            ->where('port', $parsed['port'])
            ->where('protocol', $parsed['protocol'])
            ->where(function ($query) use ($parsed) {
                if ($parsed['login'] === null && $parsed['password'] === null) {
                    $query->whereNull('login')->whereNull('password');
                } else {
                    $query->where('login', $parsed['login'])
                        ->where('password', $parsed['password']);
                }
            })
            ->first();

        if (!$proxy) {
            return response()->json([
                'success' => false,
                'message' => 'Proxy not found',
            ], 404);
        }

        $modeKey = trim($data['mode'] ?? '') ?: 'default';

        if ($resultType === 'success') {
            $proxy->success_count++;
        } else {
            $proxy->error_count++;
        }

        $info = json_decode($proxy->info ?? '{}', true);
        if (!is_array($info)) {
            $info = [];
        }
        if (!isset($info[$resultType]) || !is_array($info[$resultType])) {
            $info[$resultType] = [];
        }
        $info[$resultType][$modeKey] = ($info[$resultType][$modeKey] ?? 0) + 1;

        $proxy->info = json_encode($info, JSON_UNESCAPED_UNICODE);
        if ($this->proxyModeColumnExists()) {
            $proxy->mode = $modeKey;
        }
        $proxy->last_used_at = now();
        $proxy->active = false;

        try {
            $proxy->save();
        } catch (QueryException $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Database error while updating proxy',
            ], 500);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error while updating proxy',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Proxy stats updated',
            'proxy_id' => $proxy->id,
            'mode' => $modeKey,
            'success_count' => $proxy->success_count,
            'error_count' => $proxy->error_count,
            'info' => $info,
        ]);
    }

    private function parseProxyString(string $line): ?array
    {
        $pattern = '/^(?:(?P<protocol>https?|socks5):\/\/)?(?:(?P<login>[^:@\s]+):(?P<password>[^@:\s]+)@)?(?P<ip>\d{1,3}(?:\.\d{1,3}){3}):(?P<port>\d{2,5})$/i';

        $line = trim($line);

        if (!preg_match($pattern, $line, $matches)) {
            return null;
        }

        $protocol = strtolower($matches['protocol'] ?? 'http');

        return [
            'protocol' => $protocol,
            'login' => $matches['login'] ?? null,
            'password' => $matches['password'] ?? null,
            'ip' => $matches['ip'],
            'port' => (int) $matches['port'],
        ];
    }

    private function proxyModeColumnExists(): bool
    {
        if (!$this->modeColumnChecked) {
            $this->modeColumnExists = Schema::hasColumn('proxies', 'mode');
            $this->modeColumnChecked = true;
        }

        return $this->modeColumnExists;
    }
}

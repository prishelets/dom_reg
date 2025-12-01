<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proxy;
use Illuminate\Support\Facades\DB;

class ProxyController extends Controller
{
    public function get()
    {
        $proxy = null;

        DB::transaction(function () use (&$proxy) {
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
                $proxy->save();
            }
        }, 3);

        if (!$proxy) {
            return response()->json([
                'success' => false,
                'error' => 'No active proxies available',
            ], 404);
        }

        return response()->json([
            'success' => true,
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
}

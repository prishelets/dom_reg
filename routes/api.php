<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ProxyController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\CardController as ApiCardController;
use App\Http\Controllers\Api\SettingController as ApiSettingController;



// Здесь уже автоматически префикс /api
// То есть final URL → /api/v1/...

Route::middleware(['api', 'api.token'])->prefix('v1')->group(function () {

    Route::get('/tasks/get', [TaskController::class, 'get_tasks']);
    Route::get('/tasks/warmup/get', [TaskController::class, 'warmup']);

    Route::patch('/tasks/{task_id}/update', [TaskController::class, 'update']);
    Route::post('/tasks/{task_id}/add_account', [TaskController::class, 'addAccount']);
    Route::get('/tasks/{task_id}/send_warmup_success', [TaskController::class, 'sendWarmupSuccess']);

    Route::get('/proxies/get', [ProxyController::class, 'get']);
    Route::post('/proxies/send_success', [ProxyController::class, 'sendSuccess']);
    Route::post('/proxies/send_error', [ProxyController::class, 'sendError']);

    Route::post('/logs/add', [LogController::class, 'add']);
    Route::get('/settings/get_email_domain', [ApiSettingController::class, 'getEmailDomain']);
    Route::get('/cards/get', [ApiCardController::class, 'get']);

    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint not found',
        ], 404);
    });
});

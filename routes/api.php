<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ProxyController;
use App\Http\Controllers\Api\LogController;



// Здесь уже автоматически префикс /api
// То есть final URL → /api/v1/...

Route::middleware(['api', 'api.token'])->prefix('v1')->group(function () {

    Route::post('/add_account', [AccountController::class, 'add_account']);

    Route::get('/get_tasks', [TaskController::class, 'get_tasks']);

    Route::patch('/tasks/{task_id}/update', [TaskController::class, 'update']);

    Route::get('/proxies/get', [ProxyController::class, 'get']);

    Route::post('/logs/add', [LogController::class, 'add']);

});

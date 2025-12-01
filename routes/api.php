<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TaskController;



// Здесь уже автоматически префикс /api
// То есть final URL → /api/v1/...

Route::middleware(['api', 'api.token'])->prefix('v1')->group(function () {

    Route::get('/get_tasks', [TasksController::class, 'get_tasks']);

    Route::post('/add_account', [AccountController::class, 'add_account']);

    Route::get('/get_tasks', [TaskController::class, 'get_tasks']);

});

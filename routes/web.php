<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\ProxyController;
use App\Http\Controllers\Web\CardController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
Route::get('/', function () {
    return redirect('/tasks');
});






//Route::get('/accounts', [AccountWebController::class, 'list']);
//Route::delete('/accounts/{id}', [AccountWebController::class, 'delete']);




Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/create', [TaskController::class, 'create']);
Route::post('/tasks/store', [TaskController::class, 'store']);
Route::post('/tasks/batch-upload', [TaskController::class, 'batchStore']);
Route::post('/tasks/{id}/delete', [TaskController::class, 'delete'])->name('tasks.delete');
Route::get('/tasks/{task}/logs', [TaskController::class, 'logs']);
Route::get('/tasks/{task}/account', [TaskController::class, 'account']);

Route::get('/proxies', [ProxyController::class, 'index']);
Route::post('/proxies/store', [ProxyController::class, 'store']);
Route::get('/proxies/create', [ProxyController::class, 'create']);
Route::post('/proxies/delete-all', [ProxyController::class, 'destroyAll']);
Route::post('/proxies/{id}/delete', [ProxyController::class, 'destroy']);



Route::get('/cards', [CardController::class, 'index']);
Route::post('/cards/store', [CardController::class, 'store']);
Route::post('/cards/{id}/delete', [CardController::class, 'delete']);

Route::get('/settings', [SettingController::class, 'edit']);
Route::post('/settings', [SettingController::class, 'update']);
Route::post('/settings/cloudflare', [SettingController::class, 'updateCloudflare']);
});

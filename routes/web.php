<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\ProxyController;
use App\Http\Controllers\Web\CardController;



Route::get('/', function () {
    return redirect('/tasks');
});






//Route::get('/accounts', [AccountWebController::class, 'list']);
//Route::delete('/accounts/{id}', [AccountWebController::class, 'delete']);




Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/create', [TaskController::class, 'create']);
Route::post('/tasks/store', [TaskController::class, 'store']);
Route::post('/tasks/{id}/delete', [TaskController::class, 'delete'])->name('tasks.delete');
Route::get('/tasks/{task}/logs', [TaskController::class, 'logs']);

Route::get('/proxies', [ProxyController::class, 'index']);
Route::post('/proxies/store', [ProxyController::class, 'store']);
Route::get('/proxies/create', [ProxyController::class, 'create']);



Route::get('/cards', [CardController::class, 'index']);
Route::get('/cards/create', [CardController::class, 'create']);
Route::post('/cards/store', [CardController::class, 'store']);
Route::post('/cards/{id}/delete', [CardController::class, 'delete']);

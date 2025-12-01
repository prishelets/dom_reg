<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\ProxyController;




Route::get('/', function () {
    return view('welcome');
});






//Route::get('/accounts', [AccountWebController::class, 'list']);
//Route::delete('/accounts/{id}', [AccountWebController::class, 'delete']);




Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/create', [TaskController::class, 'create']);
Route::post('/tasks/store', [TaskController::class, 'store']);
Route::post('/tasks/{id}/delete', [TaskController::class, 'delete'])->name('tasks.delete');

Route::get('/proxies', [ProxyController::class, 'index']);
Route::post('/proxies/store', [ProxyController::class, 'store']);
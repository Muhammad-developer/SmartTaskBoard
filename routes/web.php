<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BoardController::class, 'index'])->name('home');
Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');

Route::resource('boards', BoardController::class)->except(['show']);
Route::resource('columns', ColumnController::class)->only(['store', 'update', 'destroy']);
Route::resource('tasks', TaskController::class);
Route::resource('tags', TagController::class);

// API routes for AJAX
Route::prefix('api')->group(function () {
    Route::get('boards', [BoardController::class, 'list'])->name('api.boards.list');
    Route::post('tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');
    Route::post('tasks/{task}/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::get('boards/{board}/data', [BoardController::class, 'data'])->name('boards.data');
});

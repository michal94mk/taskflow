<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
    
    // Kanban Board
    Route::get('kanban', [App\Http\Controllers\KanbanController::class, 'index'])->name('kanban.index');
    Route::patch('kanban/{task}/status', [App\Http\Controllers\KanbanController::class, 'updateStatus'])->name('kanban.updateStatus');
    
    // Comments
    Route::post('tasks/{task}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::patch('comments/{comment}', [App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Search (with rate limiting: 30 requests per minute)
    Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])
        ->middleware('throttle:30,1')
        ->name('search');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

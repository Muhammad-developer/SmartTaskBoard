<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
});

// Application Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/', [BoardController::class, 'index'])->name('home');
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::get('/dashboard', [BoardController::class, 'index'])->name('dashboard');

    Route::resource('boards', BoardController::class)->except(['show']);
    Route::resource('columns', ColumnController::class)->only(['store', 'update', 'destroy']);
    Route::resource('tasks', TaskController::class);
    Route::resource('tags', TagController::class);

    // Team Routes
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/members/invite', [TeamMemberController::class, 'invite'])->name('teams.members.invite');
    Route::patch('teams/{team}/members/{user}/role', [TeamMemberController::class, 'updateRole'])->name('teams.members.update-role');
    Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'remove'])->name('teams.members.remove');
    Route::post('teams/{team}/leave', [TeamMemberController::class, 'leave'])->name('teams.leave');

    // Comments Routes
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Attachments Routes
    Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Notifications Routes
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::delete('notifications/{id}', [NotificationController::class, 'deleteNotification'])->name('notifications.delete');

    // Export Routes
    Route::post('export/board/json', [ExportController::class, 'exportBoardJSON'])->name('export.board.json');
    Route::post('export/board/csv', [ExportController::class, 'exportBoardCSV'])->name('export.board.csv');
    Route::post('export/board/pdf', [ExportController::class, 'exportBoardPDF'])->name('export.board.pdf');

    // API routes for AJAX
    Route::prefix('api')->group(function () {
        Route::get('boards', [BoardController::class, 'list'])->name('api.boards.list');
        Route::post('tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');
        Route::post('tasks/{task}/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
        Route::get('boards/{board}/data', [BoardController::class, 'data'])->name('boards.data');
    });

    // Locale routes
    Route::post('/locale/{locale}', [LocaleController::class, 'change'])->name('locale.change');
});

// Locale routes for guests (before login)
Route::post('/locale/{locale}', [LocaleController::class, 'change'])->name('locale.change');

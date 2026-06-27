<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chat', function () {
    return view('chat', ['conversationId' => null]);
})->middleware(['auth', 'verified'])->name('chat');

Route::get('/chat/{conversationId}', function (int $conversationId) {
    return view('chat', ['conversationId' => $conversationId]);
})->middleware(['auth', 'verified'])->name('chat.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'user'])->name('user');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/models', [AdminController::class, 'models'])->name('models');
    Route::patch('/models/{model}/toggle', [AdminController::class, 'toggleModel'])->name('models.toggle');
    Route::get('/plans', [AdminController::class, 'plans'])->name('plans');
});

require __DIR__.'/auth.php';

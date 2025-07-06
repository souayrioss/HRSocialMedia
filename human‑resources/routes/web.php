<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/send/{friend_id}', [FriendController::class, 'sendRequest'])->name('friends.send');
    Route::post('/friends/accept/{id}', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/decline/{id}', [FriendController::class, 'declineRequest'])->name('friends.decline');
    Route::post('/friends/unfriend/{id}', [FriendController::class, 'unfriend'])->name('friends.unfriend');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/generate', [PostController::class, 'generateWithAI'])->name('posts.generate');

    Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');
    Route::post('/prompts', [PromptController::class, 'store'])->name('prompts.store');

    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::get('/profile/show', function () {
        return view('profile.show');
    })->middleware(['auth', 'verified'])->name('profile.show');
});

require __DIR__.'/auth.php';

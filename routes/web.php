<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// home = the whole app basically. Everything (login, register, profile,
// create/edit/delete event) happens on this one page via modals + fetch,
// see resources/views/posts/show.blade.php and public.js
Route::get('/', [PostController::class, 'show'])->name('home');
Route::get('/api/events', [PostController::class, 'getEvents'])->name('api.events');

Route::middleware('auth')->group(function () {
    // These don't render their own page anymore, they just bounce back to the
    // landing page so the account modal can pop back open there.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/api/posts/{id}/packages', [PostController::class, 'getPackages'])->name('api.posts.packages');

    Route::post('/events', [PostController::class, 'store'])->name('posts.store');
    Route::put('/events/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/events/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

require __DIR__.'/auth.php';

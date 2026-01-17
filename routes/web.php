<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\JokeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard',[JokeController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';

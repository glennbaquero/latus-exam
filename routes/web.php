<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\JokeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [JokeController::class, 'index']);

Route::get('/dashboard',[JokeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

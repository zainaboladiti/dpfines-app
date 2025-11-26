<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalFineController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FineController;



Route::get('/', [HomeController::class, 'index']);
Route::get('/database', [FineController::class, 'index']);
Route::get('/fines/{id}', [FineController::class, 'show'])->name('fine.show');

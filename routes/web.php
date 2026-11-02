<?php
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;
Route::get('/', [TourController::class, 'index'])->name('tour');
Route::post('/members', [TourController::class, 'member'])->name('members.store');
Route::post('/categories', [TourController::class, 'category'])->name('categories.store');
Route::post('/expenses', [TourController::class, 'expense'])->name('expenses.store');

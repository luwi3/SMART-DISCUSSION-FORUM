<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

// 1. Welcome Landing Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Central Landing Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Authenticated Core Features Group (Requires Login)
Route::middleware('auth')->group(function () {
    
    // User Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Quiz Module Engine Paths
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes/store', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quizID}', [QuizController::class, 'show'])->name('quizzes.show');
});

// 4. Load the external Breeze authentication paths file
require __DIR__.'/auth.php';

// 🧪 5. INDEPENDENT MODULE TESTING ROUTES
// These bypass login checks completely so you can view your progress live right now!
  Route::get('/quizzes/{quizID}/grades', [QuizController::class, 'viewGrades'])->name('quizzes.grades');

Route::get('/test-quiz-create', function() { 
    return view('quizzes.create'); 
});

Route::get('/test-quiz-show', function() { 
    return view('quizzes.show'); 
});
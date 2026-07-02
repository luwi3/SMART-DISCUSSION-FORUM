<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QuizController;

// 1. Root URL: Redirects to dashboard (or login)
Route::get('/', function () {
    return redirect('login');
});


// 🚦 1.5 The Switchboard: Handlers Laravel's default dashboard redirects
// 🚦 1.5 The Switchboard: Handlers Laravel's default dashboard redirects
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    
    // 🛠️ Temporary debug line:
    dd($user, $user->lecturer);
    
    if ($user->role === 'administrator') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->lecturer()->exists()) {
        return redirect()->route('lecturer.dashboard');
    }
    
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
// 2. Dashboard: Only for authenticated users
// 🎓 Student Dashboard Route
Route::get('/student/dashboard', function () {
    return view('dashboards.student');
})->middleware(['auth', 'verified'])->name('student.dashboard');

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
// 👨‍🏫 Lecturer Dashboard Route
Route::get('/lecturer/dashboard', function () {
    return view('dashboards.lecturer');
})->middleware(['auth', 'verified'])->name('lecturer.dashboard');

// 📋 Lecturer: Create Quiz Route
Route::get('/quizzes/create', [QuizController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('quizzes.create');
    // 💾 Lecturer: Store Quiz Route
Route::post('/quizzes', [QuizController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('quizzes.store');

// 🧪 5. INDEPENDENT MODULE TESTING ROUTES
// These bypass login checks completely so you can view your progress live right now!
  Route::get('/quizzes/{quizID}/grades', [QuizController::class, 'viewGrades'])->name('quizzes.grades');

Route::get('/test-quiz-create', function() { 
    return view('quizzes.create'); 
});

Route::get('/test-quiz-show', function() { 
    return view('quizzes.show'); 
});
// 🔑 Administrator Dashboard Route
Route::get('/admin/dashboard', function () {
    return view('dashboards.admin');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

// 2.5 Admin: Register Lecturer Route
Route::get('/admin/lecturers/create', [LecturerController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('admin.lecturers.create');

    Route::post('/admin/lecturers', [LecturerController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('admin.lecturers.store');

// 3. Authenticated Group
Route::middleware('auth')->group(function () {
    // Profile Routes (This fixes the "Route [profile.edit] not defined" error)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Forum Workspace Routes
    Route::get('/forum-workspace/{type?}/{id?}', [ForumChatController::class, 'index'])->name('chat.index');
    Route::post('/forum-workspace/{type}/{id}', [ForumChatController::class, 'store'])->name('chat.send');
});

// 4. Authentication routes
require __DIR__.'/auth.php';

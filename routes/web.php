<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminDashboardController;
use App\Models\Student;
use Carbon\Carbon;
// ==========================================
// 1. ROOT & CORE SWITCHBOARD
// ==========================================
Route::get('/', function () {
    return redirect('login');
});

// 🚦 The Switchboard: Handles Laravel's default dashboard redirects
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    
    // 🛠️ Temporary debug line:
    //dd($user, $user->lecturer);
    
    if ($user->role === 'administrator') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->lecturer()->exists()) {
        return redirect()->route('lecturer.dashboard');
    }
    
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// 2. DASHBOARD PANELS (ROLEBASED)
// ==========================================

// 🎓 Student Dashboard Route -> FIXED: Points safely to QuizController to handle active quizzes
Route::get('/student/dashboard', [QuizController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('student.dashboard');

// 👨‍🏫 Lecturer Dashboard Route
Route::get('/lecturer/dashboard', function () {
    return view('dashboards.lecturer');
})->middleware(['auth', 'verified'])->name('lecturer.dashboard');

// 🔑 Administrator Dashboard Route
// 🔑 Administrator Dashboard Route
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');

// ==========================================
// 3. ADMIN MANAGEMENT ROUTES
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/lecturers/create', [LecturerController::class, 'create'])->name('admin.lecturers.create');
    Route::post('/admin/lecturers', [LecturerController::class, 'store'])->name('admin.lecturers.store');
});


// ==========================================
// 4. AUTHENTICATED CORE FEATURES GROUP
// ==========================================
Route::middleware('auth')->group(function () {
    
    // 👤 User Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 📝 Quiz Module Engine Paths
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    
    // 🔑 FOLLOW-UP NOTE: Restored back to '/quizzes/store' to exactly match your lecturer form action 
    // and eliminate the 404 error seen in image_dc1387.png.
    Route::post('/quizzes/store', [QuizController::class, 'store'])->name('quizzes.store');
    
    // 🎯 FIXED FIX: Standardized parameter keys to handle both dynamic routing lookups natively
    Route::get('/quizzes/{quizID}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quizID}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    
    // 💬 Forum Workspace Routes
    Route::get('/forum-workspace/{type?}/{id?}', [ForumChatController::class, 'index'])->name('chat.index');
    Route::post('/forum-workspace/{type}/{id}', [ForumChatController::class, 'store'])->name('chat.store');
});


// ==========================================
// 5. INDEPENDENT MODULE TESTING ROUTES
// ==========================================
// These bypass core features check blocks so you can monitor layouts live right now!
Route::get('/quizzes/{quizID}/grades', [QuizController::class, 'viewGrades'])->name('quizzes.grades');

Route::get('/test-quiz-create', function() { 
    return view('quizzes.create'); 
});

Route::get('/test-quiz-show', function() { 
    return view('quizzes.show'); 
});
Route::get('/test-blacklist', function () {
    // 📅 1. Calculate both cutoff dates
    $blacklistCutoff = today()->subDays(3)->toDateString(); 
    $warningCutoff = today()->subDays(2)->toDateString();   

    // ⚠️ 2. WARNING: Inactive communication for 2 days (but not yet 3)
    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->whereDate('lastCommDate', '<', $warningCutoff)
        ->whereDate('lastCommDate', '>=', $blacklistCutoff)
        ->update(['status' => 'warning']);

    // ⚠️ 3. WARNING: Registered 2 days ago, never communicated
    Student::where('status', 'active')
        ->whereNull('lastCommDate')
        ->whereDate('created_at', '<', $warningCutoff)
        ->whereDate('created_at', '>=', $blacklistCutoff)
        ->update(['status' => 'warning']);

    // 🔴 4. BLACKLIST: Inactive communication for 3+ days
    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->whereDate('lastCommDate', '<', $blacklistCutoff)
        ->update(['status' => 'blacklisted']);

    // 🔴 5. BLACKLIST: Registered 3+ days ago, never communicated
    Student::where('status', 'active')
        ->whereNull('lastCommDate')
        ->whereDate('created_at', '<', $blacklistCutoff)
        ->update(['status' => 'blacklisted']);

    return response()->json([
        'status' => 'success',
        'message' => 'Admin scan complete. Warning and blacklist statuses updated.',
    ]);
})->middleware('auth');

Route::post('/admin/students/{regNo}/activate', function ($regNo) {
    $student = Student::where('regNo', $regNo)->firstOrFail();
    
    // 🔄 Reset status and update the communication date to right now
    $student->status = 'active';
    $student->lastCommDate = now(); // 👈 This resets the 5-minute countdown!
    $student->save();

    return back()->with('success', 'Student status has been reset to active successfully!');
})->middleware('auth')->where('regNo', '.*');// 👈 This allows forward slashes in the registration number!
// ==========================================
// 6. DEFAULT AUTH SYSTEM FILE LOADER
// ==========================================
require __DIR__.'/auth.php';
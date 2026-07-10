<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ParticipationController; 
use App\Http\Controllers\TopicController;        
use App\Http\Controllers\GroupDiscussionController;
use App\Http\Controllers\NotificationController;

// ==========================================
// 1. ROOT & CORE SWITCHBOARD
// ==========================================
Route::get('/', function () {
    return redirect('login');
});

// 🚦 The Switchboard: Dynamic Routing based purely on string matching values
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    
    if ($user->role === 'administrator' || $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'lecturer') {
        return redirect()->route('lecturer.dashboard');
    }
    
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//notifications
Route::get('/notifications',
[NotificationController::class,'index'])
->middleware('auth')
->name('notifications');


// ==========================================
// 2. DASHBOARD PANELS (ROLE-BASED)
// ==========================================

// 🎓 Student Dashboard Route
Route::get('/student/dashboard', [QuizController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('student.dashboard');

// 👨‍🏫 Lecturer Dashboard Route
Route::get('/lecturer/dashboard', [QuizController::class, 'lecturerDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('lecturer.dashboard');

// 📋 Standalone Lecturer Quiz List & Submissions Workspace Page
Route::get('/lecturer/quizzes', [QuizController::class, 'quizzesIndex'])
    ->middleware(['auth', 'verified'])
    ->name('lecturer.quizzes.index');

// 🔑 Administrator Dashboard Route
Route::get('/admin/dashboard', function () {
    return view('dashboards.admin');
})->middleware(['auth', 'verified'])->name('admin.dashboard');


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
    Route::post('/quizzes/store', [QuizController::class, 'store'])->name('quizzes.store');
    
    // 📁 Course Resource Document Paths
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::delete('/resources/{id}', [ResourceController::class, 'destroy'])->name('resources.destroy');
    
    // 📊 Student Participation Grade Matrix Paths
    Route::get('/participation', [ParticipationController::class, 'index'])->name('participation.index');
    Route::post('/participation/save', [ParticipationController::class, 'store'])->name('participation.store');
    
    // 🎯 Dynamic Assessment Handlers
    Route::get('/quizzes/{quizID}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quizID}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    
    // 💬 Forum Workspace Routes
    Route::post('/student/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    })->middleware('auth');

    // Topic creation deleted

    // Group creation
    Route::get('/groups/create', [GroupDiscussionController::class, 'create'])
        ->name('groups.create');

    // Forum Workspace Routes
    Route::get('/forum-workspace/{type?}/{id?}', [ForumChatController::class, 'index'])->name('chat.index');
    Route::post('/forum-workspace/{type}/{id}', [ForumChatController::class, 'store'])->name('chat.store');

    /**
     * 🟢 FIX: THE ALIAS ROUTE CATCH-NET
     * If any part of your code attempts to access /chat?topic=X, this fallback route 
     * intercepts it automatically and routes it back cleanly to your original controller layout.
     */
    Route::get('/chat', function(\Illuminate\Http\Request $request) {
        if ($request->has('topic')) {
            return redirect('/forum-workspace/topic/' . $request->query('topic'));
        }
        return redirect()->route('chat.index');
    });

    // 📝 Dedicated Topic Action Handlers
    Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
});


// ==========================================
// 5. INDEPENDENT MODULE TESTING ROUTES
// ==========================================
Route::get('/quizzes/{quizID}/grades', [QuizController::class, 'viewGrades'])->name('quizzes.grades');

Route::get('/test-quiz-create', function() { 
    return view('quizzes.create'); 
});

Route::get('/test-quiz-show', function() { 
    return view('quizzes.show'); 
});


// ==========================================
// 6. DEFAULT AUTH SYSTEM FILE LOADER
// ==========================================
require __DIR__.'/auth.php';
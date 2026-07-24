<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForumChatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ParticipationController; 
use App\Http\Controllers\TopicController;        
use App\Http\Controllers\GroupDiscussionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TopicExportController;

use App\Http\Controllers\AdminDashboardController; 
use App\Models\Student;
use Carbon\Carbon;
use App\Models\Group;
use App\Services\EmbeddingService;
use App\Services\LlamaService;


Route::get('/test-llama', function(LlamaService $llama){
    return $llama->generateTopic(
        "I don't understand database normalization"
    );
}); 

Route::get('/test-embedding', function(EmbeddingService $service){
    $result = $service->createEmbedding(
        "Students discussing database normalization"
    );
    return $result;
});

// ==========================================
// REAL-TIME BROADCAST CHANNELS
// ==========================================
Broadcast::channel('chat.{type}.{id}', function ($user, $type, $id) {
    if ($type === 'broadcast') {
        return true;
    }

    if ($type === 'group') {
        return Group::where('id', $id)
            ->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->exists();
    }

    // Fallback or course topic validation rules
    return true;
});


// ==========================================
// 1. ROOT & CORE SWITCHBOARD
// ==========================================
Route::get('/', function () {
    return redirect('login');
});

// 🚦 The Switchboard: Dynamic Routing based on user role
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    
    if ($user->role === 'administrator' || $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'lecturer') {
        return redirect()->route('lecturer.dashboard');
    }
    
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Notifications
Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware('auth')
    ->name('notifications');


// ==========================================
// 2. DASHBOARD PANELS (ROLE-BASED)
// ==========================================

// 🎓 Student Dashboard Route
Route::get('/student/dashboard', [ParticipationController::class, 'studentDashboard'])
    ->middleware(['auth', 'verified', 'no.active.quiz'])
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


// 🔄 Lightweight polling endpoint so the dashboard can detect a quiz going
// live without the student needing to refresh manually. Deliberately kept
// OUTSIDE the no.active.quiz group — it must always return JSON, never redirect.
Route::get('/student/lock-status', [ParticipationController::class, 'lockStatus'])
    ->middleware('auth')
    ->name('student.lock-status');


// ==========================================
// 4. AUTHENTICATED CORE FEATURES GROUP
// ==========================================
Route::middleware(['auth', 'no.active.quiz'])->group(function () {
    
    // 👤 User Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 📝 Quiz Module Engine Paths
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes/store', [QuizController::class, 'store'])->name('quizzes.store');
    Route::post('/quizzes/{quizID}/import', [QuizController::class, 'importCSV'])->name('quizzes.import');
    
    // 🛠️ Quiz Management (Edit, Update, Delete) & Submissions
    Route::get('/quizzes/{quizID}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quizID}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quizID}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::get('/student/quiz-submissions/{quizID}', [QuizController::class, 'showStudentSubmission'])->name('student.submissions.show');

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
    
    // 🏆 Student Quiz Scoreboard Path
    Route::get('/student/quiz-marks', [QuizController::class, 'viewStudentGrades'])->name('student.marks');
    
    // 💬 Notifications Handler
    Route::post('/student/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    });

    // 👥 Group creation views & submission processing
    Route::get('/groups/create', [GroupDiscussionController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupDiscussionController::class, 'store'])->name('groups.store');

    // 🌐 Group Browsing & Joining Core Directory Panel
    Route::get('/groups', [GroupDiscussionController::class, 'index'])->name('groups.index');
    Route::post('/groups/{id}/join', [GroupDiscussionController::class, 'join'])->name('groups.join');
    
    // 🎛️ Creator Moderation Controls
    Route::delete('/groups/{id}', [GroupDiscussionController::class, 'destroy'])->name('groups.destroy');
    Route::delete('/groups/{groupId}/remove-user/{userId}', [GroupDiscussionController::class, 'removeUser'])->name('groups.remove_user');

    // 💬 Forum Workspace & Message Deletion Routes
    Route::get('/forum-workspace/{type?}/{id?}', [ForumChatController::class, 'index'])->name('chat.index');
    Route::post('/forum-workspace/{type}/{id}', [ForumChatController::class, 'store'])->name('chat.store');
    Route::delete('/messages/{message}', [ForumChatController::class, 'destroy'])->name('chat.destroy');

    // 🟢 Alias Route Catch-Net
    Route::get('/chat', function(\Illuminate\Http\Request $request) {
        if ($request->has('topic')) {
            return redirect('/forum-workspace/topic/' . $request->query('topic'));
        }
        return redirect()->route('chat.index');
    });

    // 📝 Dedicated Topic Action Handlers
    Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');

    // 📢 Standalone Announcements Workspace Route
    Route::get('/announcements', function() {
        $announcements = \App\Models\Announcement::latest()->get();
        return view('announcements.index', compact('announcements'));
    })->name('announcements.index');
});


// ==========================================
// 5. INDEPENDENT MODULE TESTING ROUTES
// ==========================================
Route::get('/quizzes/{quizID}/grades', [QuizController::class, 'viewGrades'])->name('quizzes.grades');

Route::get('/test-quiz-create', function() { return view('quizzes.create'); });
Route::get('/test-quiz-show', function() { return view('quizzes.show'); });

Route::get('/test-blacklist', function () {
    $blacklistCutoff = today()->subDays(3)->toDateString(); 
    $warningCutoff = today()->subDays(2)->toDateString();   

    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->whereDate('lastCommDate', '<', $warningCutoff)
        ->whereDate('lastCommDate', '>=', $blacklistCutoff)
        ->update(['status' => 'warning']);

    Student::where('status', 'active')
        ->whereNull('lastCommDate')
        ->whereDate('created_at', '<', $warningCutoff)
        ->whereDate('created_at', '>=', $blacklistCutoff)
        ->update(['status' => 'warning']);

    Student::where('status', 'active')
        ->whereNotNull('lastCommDate')
        ->whereDate('lastCommDate', '<', $blacklistCutoff)
        ->update(['status' => 'blacklisted']);

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
    $student->status = 'active';
    $student->lastCommDate = now(); 
    $student->save();

    return back()->with('success', 'Student status has been reset to active successfully!');
})->middleware('auth')->where('regNo', '.*');

// ==========================================
// 6. DEFAULT AUTH SYSTEM FILE LOADER
// ==========================================

use App\Http\Controllers\TopicExportController;

// Safely added for PDF generation functionality
Route::get('/topics/{id}/export-pdf', [TopicExportController::class, 'export'])
    ->name('topics.export-pdf')
    ->middleware('auth');

require __DIR__.'/auth.php';

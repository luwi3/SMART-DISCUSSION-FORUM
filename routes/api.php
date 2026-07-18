<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\GroupDiscussionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopicController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student/dashboard-summary', [ParticipationController::class, 'studentDashboard']);
    Route::get('/student/quiz-marks', [QuizController::class, 'viewStudentGrades']);
    Route::get('/quizzes/{quizID}', [QuizController::class, 'show']);
    Route::post('/quizzes/{quizID}/submit', [QuizController::class, 'submit']);

    Route::get('/groups', [GroupDiscussionController::class, 'index']);
    Route::post('/groups', [GroupDiscussionController::class, 'store']);
    Route::post('/groups/{id}/join', [GroupDiscussionController::class, 'join']);

    Route::post('/topics', [TopicController::class, 'store']);

    Route::patch('/profile', [ProfileController::class, 'update']);

    Route::get('/notifications', function (Request $request) {
        return response()->json($request->user()->notifications()->latest()->take(20)->get());
    });
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications()->count()]);
    });
    Route::post('/notifications/mark-as-read', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    });
});
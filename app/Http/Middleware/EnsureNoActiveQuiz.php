<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Quiz;

class EnsureNoActiveQuiz
{
    /**
     * Routes a student is still allowed to hit while locked.
     * Keep 'logout' in here always — a locked student must always have an exit.
     */
    protected array $allowedRoutes = [
        'quizzes.show',
        'quizzes.take',
        'quizzes.submit',
        'logout',
        'student.dashboard', // dashboard itself renders the lock screen, so it's safe to allow
    ];

    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();

        if ($userId) {
            $student = Student::where('user_id', $userId)->first();

            if ($student && $student->courseCode && $student->regNo) {
                // 🔧 FIX: courseCode was compared with an exact match
                // (Quiz::where('courseCode', $student->courseCode)). Quiz::store()
                // saves courseCode as strtoupper() only (no trim), and Student::courseCode
                // is whatever was typed at registration/admin-creation time — so a stray
                // space or lowercase letter silently broke the match and this middleware
                // never fired. Normalize both sides the same way before comparing.
                $studentCourse = trim(strtoupper($student->courseCode));

                $liveQuizzes = Quiz::whereRaw('TRIM(UPPER(courseCode)) = ?', [$studentCourse])
                    ->where('startTime', '<=', now())
                    ->where('expiryTime', '>=', now())
                    ->get();

                if ($liveQuizzes->isNotEmpty()) {
                    $submittedQuizIDs = DB::table('quiz_submissions')
                        ->where('regNo', $student->regNo)
                        ->whereIn('quizID', $liveQuizzes->pluck('quizID'))
                        ->pluck('quizID');

                    $activeQuiz = $liveQuizzes->first(function ($quiz) use ($submittedQuizIDs) {
                        return !$submittedQuizIDs->contains($quiz->quizID);
                    });

                    if ($activeQuiz && !in_array($request->route()?->getName(), $this->allowedRoutes)) {
                        return redirect()
                            ->route('quizzes.show', ['quizID' => $activeQuiz->quizID])
                            ->with('error', 'Please finish your active quiz before continuing.');
                    }
                }
            }
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * 👨‍🏫 1. Render the clean main Lecturer Dashboard workspace.
     */
    public function lecturerDashboard()
    {
        return view('dashboards.lecturer');
    }

    /**
     * 👨‍🏫 1b. Display the master Quiz List alongside Student Marks.
     * Accessible via the "Quiz Marks" card link.
     */
    public function quizzesIndex()
    {
        $userId = Auth::id();
        $lecturer = Lecturer::where('user_id', $userId)->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'STAFF-TEST-01';

        // Fetch quizzes joined directly with student submissions, registration details, and system names
        $quizMarks = DB::table('quizzes')
            ->join('quiz_submissions', 'quizzes.quizID', '=', 'quiz_submissions.quizID')
            ->leftJoin('students', 'quiz_submissions.regNo', '=', 'students.regNo')
            ->leftJoin('users', 'students.user_id', '=', 'users.id') // 👈 Fixed: Removed bad reg_no column mapping link
            ->where('quizzes.staffNo', $staffNo)
            ->select(
                'quizzes.quizID',
                'quizzes.title as quiz_title',
                'quizzes.courseCode',
                'quiz_submissions.regNo as student_reg',
                'quiz_submissions.marks as score',
                'quiz_submissions.timeSubmitted',
                // Displays real name if available, otherwise cleanly falls back to displaying the submission regNo
                DB::raw('COALESCE(users.name, quiz_submissions.regNo) as student_name'),
                // Subquery to get total marks/questions for this quiz
                DB::raw('(SELECT COUNT(*) FROM questions WHERE questions.quizID = quizzes.quizID) as total_questions')
            )
            ->orderBy('quizzes.quizID', 'asc')
            ->orderBy('quiz_submissions.marks', 'desc')
            ->get();

        // Group the flat dataset collections by Quiz ID so the Blade layout can loop through cleanly
        $groupedQuizzes = $quizMarks->groupBy('quizID');

        return view('quizzes.index', compact('groupedQuizzes'));
    }

    // 👨‍🏫 2. Show the form to create a quiz (For Lecturers)
    public function create()
    {
        return view('quizzes.create');
    }

    // 👨‍🏫 3. Store the quiz and questions together (For Lecturers)
    public function store(Request $request)
    {
        $lecturer = Lecturer::where('user_id', Auth::id())->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'STAFF-TEST-01';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'courseCode' => 'required|string',
            'duration' => 'required|integer|min:1',
            'startTime' => 'required|date',
            'expiryTime' => 'required|date|after:startTime',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.a' => 'required|string',
            'questions.*.b' => 'required|string',
            'questions.*.c' => 'required|string',
            'questions.*.d' => 'required|string',
            'questions.*.correct' => 'required|in:A,B,C,D',
        ]);

        $quiz = Quiz::create([
            'staffNo' => $staffNo,
            'courseCode' => strtoupper($validated['courseCode']),
            'title' => $validated['title'],
            'duration' => $validated['duration'],
            'startTime' => $validated['startTime'],
            'expiryTime' => $validated['expiryTime'],
        ]);

        $resolvedQuizID = $quiz->quizID ?? $quiz->id;

        foreach ($validated['questions'] as $questionData) {
            Question::create([
                'quizID' => $resolvedQuizID,
                'question_text' => $questionData['text'],
                'option_a' => $questionData['a'],
                'option_b' => $questionData['b'],
                'option_c' => $questionData['c'],
                'option_d' => $questionData['d'],
                'correct_option' => $questionData['correct'],
            ]);
        }

        return redirect()->route('lecturer.quizzes.index')->with('success', 'Quiz published successfully!');
    }

    // 🎓 4. Open a quiz for a student
    public function show($quizID)
    {
        $quiz = Quiz::findOrFail($quizID);
        
        $userId = Auth::id();
        if (!$userId) {
            return redirect('/login')->with('error', 'Please log in to attempt the quiz.');
        }
        
        $student = Student::where('user_id', $userId)->first();
        
        $studentRegNo = $student ? $student->regNo : 'REG-USER-' . $userId;
        $studentCourse = $student ? $student->courseCode : $quiz->courseCode;

        if ($studentCourse !== $quiz->courseCode) {
            return redirect('/dashboard')->with('error', 'You are not registered for this course quiz.');
        }

        $resolvedQuizID = $quiz->quizID ?? $quiz->id;

        $submission = DB::table('quiz_submissions')
            ->where('quizID', $resolvedQuizID)
            ->where('regNo', $studentRegNo)
            ->first();

        if ($submission) {
            $expiryTime = Carbon::parse($quiz->expiryTime);
            $totalQuestionsCount = Question::where('quizID', $resolvedQuizID)->count();

            if (now()->lessThan($expiryTime)) {
                return redirect('/dashboard')->with('success', 'Your quiz has already been submitted successfully. Marks will be released once the session closes at ' . $expiryTime->format('h:i A') . '.');
            }

            return redirect('/dashboard')->with('quiz_result', "Quiz finished! Your score: {$submission->marks} / {$totalQuestionsCount} marks.");
        }

        $expiryTime = Carbon::parse($quiz->expiryTime);
        $remainingSeconds = now()->diffInSeconds($expiryTime, false);

        if ($remainingSeconds <= 0) {
            return redirect('/dashboard')->with('error', 'This quiz session has already expired.');
        }

        $questions = Question::where('quizID', $resolvedQuizID)->get();

        return view('quizzes.show', compact('quiz', 'questions', 'remainingSeconds'));
    }

    // 🎓 5. Process and Securely Grade the Quiz Submission
    public function submit(Request $request, $quizID)
    {
        $quiz = Quiz::findOrFail($quizID);
        $resolvedQuizID = $quiz->quizID ?? $quiz->id;
        
        $userId = Auth::id();
        if (!$userId) {
            return redirect('/login')->with('error', 'Session expired. Please log in again.');
        }

        $student = Student::where('user_id', $userId)->first();
        $studentRegNo = $student ? $student->regNo : 'REG-USER-' . $userId;
        
        $existingSubmission = DB::table('quiz_submissions')
            ->where('quizID', $resolvedQuizID)
            ->where('regNo', $studentRegNo)
            ->exists();

        if ($existingSubmission) {
            return redirect('/dashboard')->with('error', 'Submission already registered.');
        }

        $questions = Question::where('quizID', $resolvedQuizID)->get();
        $expiryTime = Carbon::parse($quiz->expiryTime);
        $now = now();

        $studentAnswers = $request->input('answers', []);
        $correctCount = 0;

        foreach ($questions as $question) {
            $questionKey = $question->id; 
            $studentChoice = $studentAnswers[$questionKey] ?? null;
            if ($studentChoice !== null && strtoupper($studentChoice) === strtoupper($question->correct_option)) {
                $correctCount++;
            }
        }

        $isAutoSubmit = $request->input('auto_submit', 0);

        DB::table('quiz_submissions')->insert([
            'regNo'         => $studentRegNo,
            'quizID'        => $resolvedQuizID,
            'marks'         => $correctCount,
            'timeSubmitted' => $now,
            'autoSubmit'    => $isAutoSubmit,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        if ($isAutoSubmit == 1 || $now->greaterThanOrEqualTo($expiryTime)) {
            return redirect('/dashboard')->with('quiz_result', "Quiz session closed! Your work has been saved. You secured {$correctCount} / " . $questions->count() . " marks.");
        } else {
            return redirect('/dashboard')->with('success', "Quiz submitted successfully! Your marks will be released automatically at " . $expiryTime->format('h:i A') . " once the session closes.");
        }
    }

    /**
     * 👨‍🏫 6. View Isolated Gradebook Log Sheet for an Individual Quiz
     */
    public function viewGrades($quizID)
    {
        $quiz = Quiz::findOrFail($quizID);
        $resolvedQuizID = $quiz->quizID ?? $quiz->id;

        $totalQuestionsCount = Question::where('quizID', $resolvedQuizID)->count();

        $submissions = DB::table('quiz_submissions')
            ->leftJoin('students', 'quiz_submissions.regNo', '=', 'students.regNo')
            ->leftJoin('users', 'students.user_id', '=', 'users.id') // 👈 Fixed: Removed bad reg_no column mapping link
            ->where('quiz_submissions.quizID', $resolvedQuizID)
            ->select(
                'quiz_submissions.*', 
                DB::raw('COALESCE(users.name, quiz_submissions.regNo) as student_name')
            )
            ->orderBy('quiz_submissions.marks', 'desc')
            ->get();

        return view('quizzes.grades', compact('quiz', 'submissions', 'totalQuestionsCount'));
    }

    // 💻 7. Render Student Dashboard with Active Evaluation Stream Arrays
    public function dashboard()
    {
        $userId = Auth::id();
        $student = $userId ? Student::where('user_id', $userId)->first() : null;
        $studentCourse = $student ? $student->courseCode : null;

        $activeQuizzes = Quiz::when($studentCourse, function ($query, $course) {
                return $query->where('courseCode', $course);
            })
            ->where('startTime', '<=', now())
            ->where('expiryTime', '>=', now())
            ->get();

        return view('dashboards.student', compact('activeQuizzes'));
    }
}
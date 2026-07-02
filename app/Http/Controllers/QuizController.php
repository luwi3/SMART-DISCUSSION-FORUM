<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizController extends Controller
{
    // 👨‍🏫 1. Show the form to create a quiz (For Lecturers)
    public function create()
    {
        return view('quizzes.create');
    }

    // 👨‍🏫 2. Store the quiz and questions together (For Lecturers)
    public function store(Request $request)
    {
        $lecturer = Lecturer::where('user_id', Auth::id())->firstOrFail();

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
            'staffNo' => $lecturer->staffNo,
            'courseCode' => strtoupper($validated['courseCode']),
            'title' => $validated['title'],
            'duration' => $validated['duration'],
            'startTime' => $validated['startTime'],
            'expiryTime' => $validated['expiryTime'],
        ]);

        foreach ($validated['questions'] as $questionData) {
            Question::create([
                'quizID' => $quiz->quizID,
                'question_text' => $questionData['text'],
                'option_a' => $questionData['a'],
                'option_b' => $questionData['b'],
                'option_c' => $questionData['c'],
                'option_d' => $questionData['d'],
                'correct_option' => $questionData['correct'],
            ]);
        }

        return redirect('/dashboard')->with('success', 'Quiz published successfully!');
    }

    // 🎓 3. Open a quiz for a student
    public function show($quizID)
    {
        $quiz = Quiz::findOrFail($quizID);
        
        // Testing Fallback: If not logged in, act as John Doe
        $userId = Auth::id() ?? DB::table('users')->where('email', 'john@test.com')->value('id');
        $student = Student::where('user_id', $userId)->firstOrFail();

        if ($student->courseCode !== $quiz->courseCode) {
            return redirect('/dashboard')->with('error', 'You are not registered for this course quiz.');
        }

        $existingSubmission = DB::table('quiz_submissions')
            ->where('quizID', $quiz->quizID)
            ->where('regNo', $student->regNo)
            ->exists();

        if ($existingSubmission) {
            return redirect('/dashboard')->with('error', 'You have already submitted this quiz.');
        }

        $questions = Question::where('quizID', $quiz->quizID)->get();

        return view('quizzes.show', compact('quiz', 'questions'));
    }

    // 🎓 4. Process and Securely Grade the Quiz Submission
    public function submit(Request $request, $quizID)
    {
        $quiz = Quiz::findOrFail($quizID);
        
        // Testing Fallback
        $userId = Auth::id() ?? DB::table('users')->where('email', 'john@test.com')->value('id');
        $student = Student::where('user_id', $userId)->firstOrFail();
        
        $questions = Question::where('quizID', $quiz->quizID)->get();

        $startTime = Carbon::parse($quiz->startTime);
        $endTime = $startTime->copy()->addMinutes($quiz->duration);
        $now = now();

        $studentAnswers = $request->input('answers', []);
        $correctCount = 0;

        foreach ($questions as $question) {
            $studentChoice = $studentAnswers[$question->questionID] ?? $studentAnswers[$question->id] ?? null;
            if ($studentChoice === $question->correct_option) {
                $correctCount++;
            }
        }

        DB::table('quiz_submissions')->insert([
            'regNo'         => $student->regNo,
            'quizID'        => $quiz->quizID,
            'marks'         => $correctCount,
            'timeSubmitted' => $now,
            'autoSubmit'    => $request->input('auto_submit', 0),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        if ($now->greaterThanOrEqualTo($endTime)) {
            return redirect()->to("/quizzes/{$quiz->quizID}")->with('quiz_result', "Quiz session closed! You secured {$correctCount} / " . $questions->count() . " marks.");
        } else {
            return redirect()->to("/quizzes/{$quiz->quizID}")->with('quiz_result', "Quiz submitted successfully! Your marks will be released automatically at " . $endTime->format('h:i A') . " once the session closes.");
        }
    }

    // 👨‍🏫 5. View Gradebook Log Sheet (For Lecturers / Testing Engine)
    public function viewGrades($quizID)
    {
        $quiz = Quiz::findOrFail($quizID);

        $submissions = DB::table('quiz_submissions')
            ->join('students', 'quiz_submissions.regNo', '=', 'students.regNo')
            ->where('quiz_submissions.quizID', $quiz->quizID)
            ->select('quiz_submissions.*', 'students.name as student_name')
            ->orderBy('quiz_submissions.marks', 'desc')
            ->get();

        return view('quizzes.grades', compact('quiz', 'submissions'));
    }
}
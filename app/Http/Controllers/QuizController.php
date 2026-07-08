<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        if ($student->courseCode !== $quiz->courseCode) {
            return redirect('/dashboard')->with('error', 'You are not registered for this course quiz.');
        }

        $questions = Question::where('quizID', $quiz->quizID)->get();

        return view('quizzes.show', compact('quiz', 'questions'));
    }
}
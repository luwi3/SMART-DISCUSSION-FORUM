<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * 👨‍🏫 1. Render the clean main Lecturer Dashboard workspace.
     */
    public function lecturerDashboard(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }
        return view('dashboards.lecturer');
    }

    /**
     * 👨‍🏫 1b. Display the master Quiz List alongside Student Marks.
     */
    public function quizzesIndex()
    {
        $userId = Auth::id();
        $lecturer = Lecturer::where('user_id', $userId)->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'STAFF-TEST-01';

        $quizMarks = DB::table('quizzes')
            ->join('quiz_submissions', 'quizzes.quizID', '=', 'quiz_submissions.quizID')
            ->leftJoin('students', 'quiz_submissions.regNo', '=', 'students.regNo')
            ->leftJoin('users as u1', 'students.user_id', '=', 'u1.id')
            ->leftJoin('users as u2', function($join) {
                $join->on(DB::raw("CAST(REGEXP_REPLACE(quiz_submissions.regNo, '[^0-9]', '') AS UNSIGNED)"), '=', 'u2.id')
                     ->where(function($query) {
                         $query->where('quiz_submissions.regNo', 'LIKE', 'USR-%')
                               ->orWhere('quiz_submissions.regNo', 'LIKE', 'REG-USER-%')
                               ->orWhere('quiz_submissions.regNo', 'LIKE', 'REG-TEST-%');
                     });
            })
            ->where('quizzes.staffNo', $staffNo)
            ->select(
                'quizzes.quizID',
                'quizzes.title as quiz_title',
                'quizzes.courseCode',
                'quiz_submissions.regNo as student_reg',
                'quiz_submissions.marks as score',
                'quiz_submissions.timeSubmitted',
                DB::raw('COALESCE(u1.name, u2.name, quiz_submissions.regNo) as student_name'),
                DB::raw('(SELECT COUNT(*) FROM questions WHERE questions.quizID = quizzes.quizID) as total_questions')
            )
            ->orderBy('quizzes.quizID', 'asc')
            ->orderBy('quiz_submissions.marks', 'desc')
            ->get();

        $groupedQuizzes = $quizMarks->groupBy('quizID');

        // Also fetch all created quizzes for lecturer management actions (Edit/Delete)
        $allLecturerQuizzes = Quiz::where('staffNo', $staffNo)->get();

        return view('quizzes.index', compact('groupedQuizzes', 'allLecturerQuizzes'));
    }

    /**
     * 👨‍🏫 2. Show the form to create a quiz (For Lecturers)
     */
    public function create()
    {
        return view('quizzes.create');
    }

    /**
     * 👨‍🏫 3. Store the quiz, create its announcement, and handle questions
     */
    public function store(Request $request)
    {
        $lecturer = Lecturer::where('user_id', Auth::id())->first();
        $staffNo = $lecturer ? $lecturer->staffNo : 'STAFF-TEST-01';

        $rules = [
            'title' => 'required|string|max:255',
            'courseCode' => 'required|string',
            'duration' => 'required|integer|min:1',
            'startTime' => 'required|date',
            'expiryTime' => 'required|date|after:startTime',
            'creation_method' => 'required|in:manual,csv',
        ];

        if ($request->input('creation_method') === 'manual') {
            $rules['questions'] = 'required|array|min:1';
            $rules['questions.*.text'] = 'required|string';
            $rules['questions.*.a'] = 'required|string';
            $rules['questions.*.b'] = 'required|string';
            $rules['questions.*.c'] = 'required|string';
            $rules['questions.*.d'] = 'required|string';
            $rules['questions.*.correct'] = 'required|in:A,B,C,D';
        } else {
            $rules['filtered_questions_json'] = 'required|string';
        }

        $validated = $request->validate($rules);

        return DB::transaction(function () use ($validated, $staffNo, $request) {
            $quiz = Quiz::create([
                'staffNo' => $staffNo,
                'courseCode' => strtoupper($validated['courseCode']),
                'title' => $validated['title'],
                'duration' => $validated['duration'],
                'startTime' => $validated['startTime'],
                'expiryTime' => $validated['expiryTime'],
            ]);

            $resolvedQuizID = $quiz->quizID;
            $questionsToInsert = [];

            if ($validated['creation_method'] === 'manual') {
                foreach ($validated['questions'] as $questionData) {
                    $questionsToInsert[] = [
                        'quizID' => $resolvedQuizID,
                        'question_text' => $questionData['text'],
                        'option_a' => $questionData['a'],
                        'option_b' => $questionData['b'],
                        'option_c' => $questionData['c'],
                        'option_d' => $questionData['d'],
                        'correct_option' => strtoupper(trim($questionData['correct'])),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            } else {
                $decodedQuestions = json_decode($validated['filtered_questions_json'], true);

                if (!is_array($decodedQuestions) || empty($decodedQuestions)) {
                    return redirect()->back()->withErrors(['filtered_questions_json' => 'Failed to process selected question sets.']);
                }

                foreach ($decodedQuestions as $q) {
                    $questionsToInsert[] = [
                        'quizID' => $resolvedQuizID,
                        'question_text' => $q['text'] ?? 'Missing Question Text',
                        'option_a' => $q['a'] ?? '',
                        'option_b' => $q['b'] ?? '',
                        'option_c' => $q['c'] ?? '',
                        'option_d' => $q['d'] ?? '',
                        'correct_option' => strtoupper(trim($q['correct'] ?? 'A')),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($questionsToInsert)) {
                Question::insert($questionsToInsert);
            }

            // Automatically post an announcement for the students
            Announcement::create([
                'title' => 'New Quiz Available: ' . $quiz->title,
                'courseCode' => strtoupper($quiz->courseCode),
                'message' => "A new quiz for course {$quiz->courseCode} has been scheduled.\n" .
                             "Title: {$quiz->title}\n" .
                             "Duration: {$quiz->duration} minutes\n" .
                             "Starts At: " . Carbon::parse($quiz->startTime)->format('M d, Y H:i') . "\n" .
                             "Ends At: " . Carbon::parse($quiz->expiryTime)->format('M d, Y H:i'),
            ]);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'quizID' => $resolvedQuizID]);
            }

            return redirect()->route('lecturer.quizzes.index')->with('success', 'Quiz published and announcement sent successfully!');
        });
    }

    /**
     * 👨‍🏫 3b. Bulk Import Questions directly onto an Existing Quiz with Review Step
     */
    public function importCSV(Request $request, $quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();
        $resolvedQuizID = $quiz->quizID;

        // Step 2: Save Confirmed & Reviewed Questions
        if ($request->isMethod('post') && $request->has('confirmed_questions')) {
            $questions = json_decode($request->input('confirmed_questions'), true);

            if (!is_array($questions) || empty($questions)) {
                return redirect()->back()->with('error', 'No valid questions found to import.');
            }

            $insertedCount = 0;
            foreach ($questions as $row) {
                if (empty($row['text'])) continue;

                Question::create([
                    'quizID'         => $resolvedQuizID,
                    'question_text'  => $row['text'],
                    'option_a'       => $row['a'] ?? '',
                    'option_b'       => $row['b'] ?? '',
                    'option_c'       => $row['c'] ?? '',
                    'option_d'       => $row['d'] ?? '',
                    'correct_option' => strtoupper(trim($row['correct'] ?? 'A')),
                ]);
                $insertedCount++;
            }

            return redirect()->route('lecturer.quizzes.index')
                ->with('success', "Successfully reviewed and imported {$insertedCount} questions!");
        }

        // Step 1: Upload CSV and Parse Questions for Review View
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $filePath = $request->file('csv_file')->getRealPath();
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            fgetcsv($handle, 1000, ','); // Skip header row
            $parsedQuestions = [];

            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (empty($row[0])) continue;

                $parsedQuestions[] = [
                    'text'    => $row[0],
                    'a'       => $row[1] ?? '',
                    'b'       => $row[2] ?? '',
                    'c'       => $row[3] ?? '',
                    'd'       => $row[4] ?? '',
                    'correct' => strtoupper(trim($row[5] ?? 'A')),
                ];
            }
            fclose($handle);

            // Return to review blade before inserting into DB
            return view('quizzes.review_csv', compact('quiz', 'parsedQuestions'));
        }

        return redirect()->back()->with('error', 'Failed to open CSV.');
    }

    /**
     * 👨‍🏫 3c. Edit Quiz Form (For Lecturers)
     */
    public function edit($quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();
        $questions = Question::where('quizID', $quiz->quizID)->get();

        return view('quizzes.edit', compact('quiz', 'questions'));
    }

    /**
     * 👨‍🏫 3d. Update Quiz and Questions Configuration
     */
    public function update(Request $request, $quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'courseCode' => 'required|string',
            'duration' => 'required|integer|min:1',
            'startTime' => 'required|date',
            'expiryTime' => 'required|date|after:startTime',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|integer',
            'questions.*.text' => 'required_with:questions|string',
            'questions.*.a' => 'required_with:questions|string',
            'questions.*.b' => 'required_with:questions|string',
            'questions.*.c' => 'required_with:questions|string',
            'questions.*.d' => 'required_with:questions|string',
            'questions.*.correct' => 'required_with:questions|in:A,B,C,D',
        ]);

        DB::transaction(function () use ($quiz, $validated) {
            $quiz->update([
                'title' => $validated['title'],
                'courseCode' => strtoupper($validated['courseCode']),
                'duration' => $validated['duration'],
                'startTime' => $validated['startTime'],
                'expiryTime' => $validated['expiryTime'],
            ]);

            if (isset($validated['questions'])) {
                $existingIds = [];
                foreach ($validated['questions'] as $qData) {
                    if (!empty($qData['id'])) {
                        $existingIds[] = $qData['id'];
                        Question::where('id', $qData['id'])->where('quizID', $quiz->quizID)->update([
                            'question_text' => $qData['text'],
                            'option_a' => $qData['a'],
                            'option_b' => $qData['b'],
                            'option_c' => $qData['c'],
                            'option_d' => $qData['d'],
                            'correct_option' => strtoupper(trim($qData['correct'])),
                        ]);
                    } else {
                        $newQ = Question::create([
                            'quizID' => $quiz->quizID,
                            'question_text' => $qData['text'],
                            'option_a' => $qData['a'],
                            'option_b' => $qData['b'],
                            'option_c' => $qData['c'],
                            'option_d' => $qData['d'],
                            'correct_option' => strtoupper(trim($qData['correct'])),
                        ]);
                        $existingIds[] = $newQ->id;
                    }
                }
                // Cleanup removed questions
                Question::where('quizID', $quiz->quizID)->whereNotIn('id', $existingIds)->delete();
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Quiz updated successfully!']);
        }

        return redirect()->route('lecturer.quizzes.index')->with('success', 'Quiz updated successfully!');
    }

    /**
     * 👨‍🏫 3e. Delete Quiz along with Submissions & Questions
     */
    public function destroy(Request $request, $quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();

        DB::transaction(function () use ($quiz) {
            Question::where('quizID', $quiz->quizID)->delete();
            DB::table('quiz_submissions')->where('quizID', $quiz->quizID)->delete();
            $quiz->delete();
        });

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Quiz deleted successfully']);
        }

        return redirect()->route('lecturer.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    /**
     * 🎓 4. Open a quiz for a student with strict window enforcement
     */
    public function show(Request $request, $quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();

        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Please log in to attempt the quiz.'], 401);
            }
            return redirect('/login')->with('error', 'Please log in to attempt the quiz.');
        }

        $student = Student::where('user_id', $userId)->first();
        $studentRegNo = $student ? $student->regNo : 'USR-' . $userId;
        $studentCourse = $student ? trim(strtoupper($student->courseCode)) : null;
        $quizCourse = trim(strtoupper($quiz->courseCode));

        if ($studentCourse && $studentCourse !== $quizCourse) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'You are not registered for this course quiz.'], 403);
            }
            return redirect('/dashboard')->with('error', 'You are not registered for this course quiz.');
        }

        $resolvedQuizID = $quiz->quizID;

        $now = now();
        $startTime = Carbon::parse($quiz->startTime);
        $expiryTime = Carbon::parse($quiz->expiryTime);

        if ($now->lessThan($startTime)) {
            $msg = 'This quiz has not started yet. It opens at ' . $startTime->format('M d, Y H:i');
            if ($request->wantsJson()) {
                return response()->json(['error' => $msg], 403);
            }
            return redirect('/dashboard')->with('error', $msg);
        }

        if ($now->greaterThan($expiryTime)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'This quiz session has already expired.'], 403);
            }
            return redirect('/dashboard')->with('error', 'This quiz session has already expired.');
        }

        $submission = DB::table('quiz_submissions')
            ->where('quizID', $resolvedQuizID)
            ->where('regNo', $studentRegNo)
            ->first();

        if ($submission) {
            $totalQuestionsCount = Question::where('quizID', $resolvedQuizID)->count();
            $msg = "Quiz finished! Your score: {$submission->marks} / {$totalQuestionsCount} marks.";
            if ($request->wantsJson()) {
                return response()->json(['error' => $msg, 'already_submitted' => true, 'score' => $submission->marks, 'total' => $totalQuestionsCount], 409);
            }
            return redirect('/dashboard')->with('quiz_result', $msg);
        }

        $remainingSeconds = $now->diffInSeconds($expiryTime, false);
        $questions = Question::where('quizID', $resolvedQuizID)->get();

        if ($request->wantsJson()) {
            // Strip correct_option so the client can't just read the answer key
            $safeQuestions = $questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'option_a' => $q->option_a,
                    'option_b' => $q->option_b,
                    'option_c' => $q->option_c,
                    'option_d' => $q->option_d,
                ];
            });
            return response()->json(compact('quiz', 'remainingSeconds') + ['questions' => $safeQuestions]);
        }

        return view('quizzes.show', compact('quiz', 'questions', 'remainingSeconds'));
    }

    /**
     * 🎓 5. Process and Grade Quiz Submissions
     */
    public function submit(Request $request, $quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();
        $resolvedQuizID = $quiz->quizID;

        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Session expired.'], 401);
            }
            return redirect('/login')->with('error', 'Session expired.');
        }

        $student = Student::where('user_id', $userId)->first();
        $studentRegNo = $student ? $student->regNo : 'USR-' . $userId;

        $existingSubmission = DB::table('quiz_submissions')
            ->where('quizID', $resolvedQuizID)
            ->where('regNo', $studentRegNo)
            ->exists();

        if ($existingSubmission) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Submission already registered.'], 409);
            }
            return redirect('/dashboard')->with('error', 'Submission already registered.');
        }

        $questions = Question::where('quizID', $resolvedQuizID)->get();
        $expiryTime = Carbon::parse($quiz->expiryTime);
        $now = now();

        $studentAnswers = $request->input('answers', []);
        $correctCount = 0;

        foreach ($questions as $question) {
            $questionKey = (string) $question->id;
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

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'score' => $correctCount, 'total' => $questions->count()]);
        }

        if ($isAutoSubmit == 1 || $now->greaterThanOrEqualTo($expiryTime)) {
            return redirect('/dashboard')->with('quiz_result', "Quiz saved. Score: {$correctCount} / " . $questions->count());
        }

        return redirect('/dashboard')->with('success', "Quiz submitted successfully!");
    }

    /**
     * 👨‍🏫 6. View Gradebook for an Individual Quiz
     */
    public function viewGrades($quizID)
    {
        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();
        $resolvedQuizID = $quiz->quizID;
        $totalQuestionsCount = Question::where('quizID', $resolvedQuizID)->count();

        $submissions = DB::table('quiz_submissions')
            ->leftJoin('students', 'quiz_submissions.regNo', '=', 'students.regNo')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->where('quiz_submissions.quizID', $resolvedQuizID)
            ->select('quiz_submissions.*', DB::raw('COALESCE(users.name, quiz_submissions.regNo) as student_name'))
            ->orderBy('quiz_submissions.marks', 'desc')
            ->get();

        return view('quizzes.grades', compact('quiz', 'submissions', 'totalQuestionsCount'));
    }

    /**
     * 💻 7. Render Student Dashboard with Active Evaluation Stream Arrays & Lockdown Control
     */
    public function dashboard()
    {
        $userId = Auth::id();
        $student = $userId ? Student::where('user_id', $userId)->first() : null;
        $studentRegNo = $student ? $student->regNo : 'USR-' . $userId;
        $studentCourse = $student ? trim(strtoupper($student->courseCode)) : null;

        // Fetch all current active quizzes for the student's course
        $activeQuizzes = Quiz::when($studentCourse, function ($query, $course) {
                return $query->where('courseCode', $course);
            })
            ->where('startTime', '<=', now())
            ->where('expiryTime', '>=', now())
            ->get();

        // Identify active quizzes that the student has NOT completed yet
        $unsubmittedQuizzes = $activeQuizzes->filter(function ($quiz) use ($studentRegNo) {
            return !DB::table('quiz_submissions')
                ->where('quizID', $quiz->quizID)
                ->where('regNo', $studentRegNo)
                ->exists();
        });

        // Lockdown flags to freeze student dashboard when an active quiz is pending
        $hasActiveQuiz = $unsubmittedQuizzes->isNotEmpty();
        $pendingQuiz = $unsubmittedQuizzes->first();

        return view('dashboards.student', compact('activeQuizzes', 'unsubmittedQuizzes', 'hasActiveQuiz', 'pendingQuiz'));
    }

    /**
     * 🎓 8. Render Personal Performance Grade Sheets for Students
     */
    public function viewStudentGrades(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect('/login')->with('error', 'Please log in to check grades.');
        }

        $student = Student::where('user_id', $userId)->first();
        $studentRegNo = $student ? $student->regNo : 'USR-' . $userId;

        $grades = DB::table('quiz_submissions')
            ->join('quizzes', 'quiz_submissions.quizID', '=', 'quizzes.quizID')
            ->where('quiz_submissions.regNo', $studentRegNo)
            ->select(
                'quizzes.quizID',
                'quizzes.title as quiz_title',
                'quizzes.courseCode',
                'quiz_submissions.marks as score',
                'quiz_submissions.timeSubmitted',
                DB::raw('(SELECT COUNT(*) FROM questions WHERE questions.quizID = quizzes.quizID) as total_questions')
            )
            ->orderBy('quiz_submissions.timeSubmitted', 'desc')
            ->get();

        if ($request->wantsJson()) {
            return response()->json(compact('grades'));
        }

        return view('quizzes.student_marks', compact('grades'));
    }

    /**
     * 🎓 8b. View Specific Submission Details for Student Breakdown
     */
    public function showStudentSubmission(Request $request, $quizID)
    {
        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect('/login')->with('error', 'Please log in.');
        }

        $student = Student::where('user_id', $userId)->first();
        $studentRegNo = $student ? $student->regNo : 'USR-' . $userId;

        $quiz = Quiz::where('quizID', $quizID)->firstOrFail();
        $submission = DB::table('quiz_submissions')
            ->where('quizID', $quiz->quizID)
            ->where('regNo', $studentRegNo)
            ->firstOrFail();

        $totalQuestions = Question::where('quizID', $quiz->quizID)->count();

        if ($request->wantsJson()) {
            return response()->json(compact('quiz', 'submission', 'totalQuestions'));
        }

        return view('quizzes.student_submission_details', compact('quiz', 'submission', 'totalQuestions'));
    }
}
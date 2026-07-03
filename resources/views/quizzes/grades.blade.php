<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradebook - {{ $quiz->title ?? 'Quiz Results' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; padding: 40px; display: flex; justify-content: center; min-height: 100vh; }
        .gradebook-container { max-width: 1000px; width: 100%; background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); align-self: flex-start; }
        
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 24px; margin-bottom: 24px; }
        .quiz-info h1 { font-size: 22px; color: #0c2340; font-weight: 700; }
        .quiz-info p { font-size: 14px; color: #64748b; margin-top: 4px; }
        .stats-badge { background: #eff6ff; color: #2563eb; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; border: 1px solid #bfdbfe; }
        
        /* Navigation Return Button Style */
        .btn-return { background-color: #0c2340; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 700; display: inline-block; transition: background 0.2s; margin-bottom: 20px; }
        .btn-return:hover { background-color: #1e3a8a; }

        /* Table Layout Styling */
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
        th { background-color: #f8fafc; color: #475569; font-size: 13px; font-weight: 700; padding: 14px 18px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 16px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #334155; }
        tr:hover { background-color: #f8fafc; }
        
        .status { font-weight: 700; font-size: 12px; padding: 4px 8px; border-radius: 4px; }
        .status-auto { background: #fee2e2; color: #b91c1c; }
        .status-normal { background: #dcfce7; color: #16a34a; }
        .score-display { font-weight: 700; color: #0c2340; }

        /* 🎓 Personalized Student Score Card UI */
        .student-score-layout { text-align: center; padding: 40px 20px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; max-width: 550px; margin: 20px auto 0 auto; }
        .score-huge { font-size: 64px; font-weight: 800; color: #2563eb; margin: 15px 0; }
        .score-huge.passed { color: #16a34a; }
        .student-detail-row { margin-top: 15px; border-top: 1px dashed #e2e8f0; padding-top: 15px; font-size: 14px; color: #475569; line-height: 1.6; }
    </style>
</head>
<body>

<div class="gradebook-container">
    
    <!-- Role-Based Navigation Back Button -->
    @if(Auth::user()->role === 'lecturer')
        <a href="/lecturer/dashboard" class="btn-return">⬅️ Back to Workspace</a>
    @else
        <a href="/student/dashboard" class="btn-return">⬅️ Back to Dashboard</a>
    @endif

    <div class="header">
        <div class="quiz-info">
            <h1>Quiz Assessment Gradebook</h1>
            <p>Course Code: <strong>{{ $quiz->courseCode ?? 'N/A' }}</strong> | Title: {{ $quiz->title ?? 'N/A' }}</p>
        </div>
        @if(Auth::user()->role === 'lecturer')
            <div class="stats-badge">
                Total Submissions: {{ $submissions->count() }}
            </div>
        @endif
    </div>

    <!-- 👨‍🏫 VIEW 1: LECTURER MODE (Shows the full master submission spreadsheet log) -->
    @if(Auth::user()->role === 'lecturer')
        <table>
            <thead>
                <tr>
                    <th>Registration No.</th>
                    <th>Student Name</th>
                    <th>Time Submitted</th>
                    <th>Submission Mode</th>
                    <th>Marks Secured</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td><strong>{{ $submission->regNo }}</strong></td>
                        <td>{{ $submission->student_name ?? 'System Enrolled Student' }}</td>
                        <td>{{ $submission->timeSubmitted ? \Carbon\Carbon::parse($submission->timeSubmitted)->format('d M Y, h:i A') : 'N/A' }}</td>
                        <td>
                            @if($submission->autoSubmit || (isset($submission->is_submitted_automatically) && $submission->is_submitted_automatically))
                                <span class="status status-auto">⏰ Timed Out (Auto)</span>
                            @else
                                <span class="status status-normal">✅ Submitted On-Time</span>
                            @endif
                        </td>
                        <td><span class="score-display">{{ $submission->marks }} Marks</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 40px; font-weight: 500;">No students have submitted this assessment yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    <!-- 🎓 VIEW 2: STUDENT MODE (Displays the secure single evaluated student card block) -->
    @else
        <div class="student-score-layout">
            <h3 style="color: #334155; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">Your Evaluated Score</h3>
            
            @php 
                // CRITICAL ATTEMPT MATCH FIX: Extract authenticated student identity row out of collection 
                $mySubmission = $submissions->firstWhere('regNo', Auth::user()->student->regNo ?? ''); 
            @endphp
            
            @if(!$mySubmission)
                <div class="score-huge" style="color: #64748b;">--</div>
                <p style="color: #64748b; font-size: 14px; padding: 10px;">Your response history was not completed or hasn't been closed out in our records yet.</p>
            @else
                <div class="score-huge passed">{{ $mySubmission->marks }} Marks</div>
                
                <div class="student-detail-row">
                    <p>Student Identifier: <strong>{{ $mySubmission->regNo }}</strong></p>
                    <p>Logged Processing Date: <strong>{{ $mySubmission->timeSubmitted ? \Carbon\Carbon::parse($mySubmission->timeSubmitted)->format('d M Y, h:i A') : 'Instant' }}</strong></p>
                    
                    <div style="margin-top: 15px;">
                        @if($mySubmission->autoSubmit || (isset($mySubmission->is_submitted_automatically) && $mySubmission->is_submitted_automatically))
                            <span class="status status-auto" style="display: block; padding: 10px; font-size: 13px;">
                                ⏱️ Auto-Submitted: Your quiz assessment countdown completed. The platform locked the session and evaluated all responses logged up to that boundary line. No extra extension time was permitted.
                            </span>
                        @else
                            <span class="status status-normal" style="display: block; padding: 10px; font-size: 13px;">
                                🎉 Successfully Received: Handed in safely and fully recorded within your running countdown limits.
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

</body>
</html>
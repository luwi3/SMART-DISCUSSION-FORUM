<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: #f4f6f9; padding: 20px; display: flex; justify-content: center; }
        .quiz-container { max-width: 800px; width: 100%; background: white; border-radius: 8px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        
        /* Header Title */
        .quiz-title { display: flex; align-items: center; gap: 10px; font-size: 20px; color: #1e293b; margin-bottom: 20px; font-weight: 600; }
        
        /* Timer Bar */
        .timer-bar { border: 1px solid #fca5a5; background-color: #fff5f5; border-radius: 6px; text-align: center; padding: 10px; margin-bottom: 20px; }
        .timer-label { font-size: 12px; color: #7f1d1d; text-transform: uppercase; letter-spacing: 0.5px; }
        .timer-display { font-size: 24px; color: #991b1b; font-weight: bold; margin-top: 2px; }

        /* Meta Details Grid */
        .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 15px; }
        @media(max-width: 600px) { .meta-grid { grid-template-columns: 1fr 1fr; } }
        .meta-item { display: flex; flex-direction: column; }
        .meta-item label { font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px; }
        .meta-item input { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; font-size: 14px; color: #334155; pointer-events: none; }

        /* Instructions Banner */
        .instructions-box { background: #f1f5f9; border-radius: 6px; padding: 12px 16px; margin-bottom: 25px; border: 1px solid #e2e8f0; }
        .instruction-tag { background: white; font-size: 11px; font-weight: 700; color: #475569; padding: 2px 8px; border-radius: 12px; border: 1px solid #cbd5e1; display: inline-block; margin-bottom: 6px; }
        .instruction-text { font-size: 14px; color: #334155; }

        /* Question Area */
        .question-block { border-bottom: 1px dashed #e2e8f0; padding-bottom: 20px; margin-bottom: 25px; }
        .question-block:last-of-type { border-bottom: none; }
        .question-title { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 15px; }
        .options-list { display: flex; flex-direction: column; gap: 12px; }
        .option-item { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #334155; cursor: pointer; padding: 8px; border-radius: 6px; transition: background 0.1s; }
        .option-item:hover { background-color: #f8fafc; }
        .option-item input[type="radio"] { margin-top: 4px; cursor: pointer; }

        /* Navigation Button Bars */
        .footer-navigation { display: flex; justify-content: flex-end; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .btn-nav { padding: 10px 24px; font-size: 14px; font-weight: 600; border-radius: 6px; cursor: pointer; background: white; border: 1px solid #cbd5e1; color: #475569; transition: background 0.1s; }
        .btn-success { background: #0b2265; color: white; border: none; }
        .btn-success:hover { background: #1e3a8a; }
    </style>
</head>
<body>

<div class="quiz-container">
    <div class="quiz-title">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <span>{{ $quiz->title }}</span>
    </div>

    <div class="timer-bar">
        <div class="timer-label">Time Remaining</div>
        <div class="timer-display" id="clock">--:--:--</div>
    </div>

    <div class="meta-grid">
        <div class="meta-item">
            <label>Quiz ID</label>
            <input type="text" value="{{ $quiz->quizID }}">
        </div>
        <div class="meta-item">
            <label>Course Code</label>
            <input type="text" value="{{ $quiz->courseCode }}">
        </div>
        <div class="meta-item">
            <label>Start Time</label>
            <input type="text" value="{{ \Carbon\Carbon::parse($quiz->startTime)->format('h:i A') }}">
        </div>
        <div class="meta-item">
            <label>Duration</label>
            <input type="text" value="{{ $quiz->duration }} min">
        </div>
    </div>

    <div class="instructions-box">
        <div class="instruction-tag">Instructions</div>
        <div class="instruction-text">Ensure you track your timer constraint. Do not refresh or exit the submission layout once active.</div>
    </div>

    <form id="quizForm" method="POST" action="/quizzes/{{ $quiz->quizID }}/submit">
        @csrf
        
        <input type="hidden" name="auto_submit" id="autoSubmitInput" value="0">
        
        @foreach($questions as $index => $question)
            <div class="question-block">
                <p class="question-title"><strong>Q{{ $index + 1 }}.</strong> {{ $question->question_text }}</p>
                
                <div class="options-list">
                    <label class="option-item">
                        <input type="radio" name="answers[{{ $question->id }}]" value="A" required>
                        <span>A. {{ $question->option_a }}</span>
                    </label>
                    <label class="option-item">
                        <input type="radio" name="answers[{{ $question->id }}]" value="B">
                        <span>B. {{ $question->option_b }}</span>
                    </label>
                    <label class="option-item">
                        <input type="radio" name="answers[{{ $question->id }}]" value="C">
                        <span>C. {{ $question->option_c }}</span>
                    </label>
                    <label class="option-item">
                        <input type="radio" name="answers[{{ $question->id }}]" value="D">
                        <span>D. {{ $question->option_d }}</span>
                    </label>
                </div>
            </div>
        @endforeach

        <div class="footer-navigation">
            <button type="submit" class="btn-nav btn-success">SUBMIT QUIZ</button>
        </div>
    </form>
</div>

<script>
    // ⏰ Calculate the absolute end deadline timestamp using ISO standard formatting
    const startTime = new Date("{{ \Carbon\Carbon::parse($quiz->startTime)->toIso8601String() }}").getTime();
    const durationMinutes = {{ $quiz->duration }};
    const endTime = startTime + (durationMinutes * 60 * 1000);

    const display = document.getElementById('clock');

    function startTimer() {
        const timerInterval = setInterval(() => {
            const now = new Date().getTime();
            let totalSeconds = Math.floor((endTime - now) / 1000);

            // 🛑 Strict Constraint: Session expired or forced auto-submit condition triggered
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                display.textContent = "00:00:00";
                document.getElementById('autoSubmitInput').value = "1";
                alert("The quiz session has officially closed! Submitting your work now.");
                document.getElementById('quizForm').submit();
                return;
            }

            let hours = Math.floor(totalSeconds / 3600);
            let minutes = Math.floor((totalSeconds % 3600) / 60);
            let seconds = totalSeconds % 60;

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = `${hours}:${minutes}:${seconds}`;
        }, 1000);
    }

    window.onload = startTimer;
</script>

</body>
</html>
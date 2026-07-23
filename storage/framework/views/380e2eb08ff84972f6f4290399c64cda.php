<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: #f4f6f9; padding: 40px 20px; display: flex; justify-content: center; }
        .form-container { max-width: 800px; width: 100%; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; }
        .header h2 { color: #1e293b; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #475569; }
        input[type="text"], input[type="number"], input[type="datetime-local"], select, textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #334155; outline: none; }
        input:focus, textarea:focus, select:focus { border-color: #0b2265; box-shadow: 0 0 0 3px rgba(11, 34, 101, 0.1); }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        /* Method Selector Styling */
        .import-method-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 25px 0; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1; }
        .method-option { display: flex; align-items: center; gap: 10px; padding: 10px; background: white; border: 1px solid #e2e8f0; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
        .method-option:hover { border-color: #0b2265; }
        .method-option input[type="radio"] { accent-color: #0b2265; width: 18px; height: 18px; cursor: pointer; }
        .method-option label { margin: 0; cursor: pointer; font-size: 14px; color: #1e293b; display: flex; flex-direction: column; gap: 2px; }
        .method-option label span { font-size: 11px; color: #64748b; font-weight: 400; }

        /* Questions Styling */
        .section-title { font-size: 18px; color: #1e293b; margin: 30px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .question-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 20px; position: relative; }
        .btn-add { background: #10b981; color: white; border: none; padding: 8px 14px; font-weight: 600; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .btn-add:hover { background: #059669; }
        .btn-remove { background: #ef4444; color: white; border: none; padding: 6px 12px; font-weight: 600; border-radius: 4px; cursor: pointer; font-size: 12px; margin-top: 10px; float: right; }
        .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
        
        /* CSV Box & Live Preview Area Styling */
        .csv-upload-panel { background: #fffbf7; border: 1px solid #fed7aa; padding: 20px; border-radius: 8px; margin-top: 20px; display: none; }
        .preview-box { margin-top: 20px; background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 15px; display: none; max-height: 300px; overflow-y: auto; }
        .preview-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
        .preview-table th { background: #0b2265; color: white; padding: 8px; font-weight: 600; }
        .preview-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; color: #334155; }

        .btn-submit { background: #0b2265; color: white; border: none; padding: 12px 20px; font-weight: 600; border-radius: 6px; cursor: pointer; font-size: 16px; width: 100%; margin-top: 20px; transition: background 0.2s; }
        .btn-submit:hover { background: #1e3a8a; }
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="header">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <h2>Setup Discussion Forum Quiz</h2>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert-error">
            <strong>Whoops! Please fix the errors below:</strong>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('quizzes.store')); ?>" enctype="multipart/form-data" id="quiz-form">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="title">Quiz Title / Topic</label>
            <input type="text" id="title" name="title" value="<?php echo e(old('title')); ?>" required placeholder="e.g., Software Engineering Fundamentals">
        </div>

        <div class="row">
            <div class="form-group">
                <label for="courseCode">Course Code</label>
                <input type="text" id="courseCode" name="courseCode" value="<?php echo e(old('courseCode')); ?>" required placeholder="e.g., BIT 2201">
            </div>
            <div class="form-group">
                <label for="duration">Duration (In Minutes)</label>
                <input type="number" id="duration" name="duration" value="<?php echo e(old('duration')); ?>" required placeholder="e.g., 45">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="startTime">Start Date & Time</label>
                <input type="datetime-local" id="startTime" name="startTime" value="<?php echo e(old('startTime')); ?>" required>
            </div>
            <div class="form-group">
                <label for="expiryTime">Expiry Date & Time</label>
                <input type="datetime-local" id="expiryTime" name="expiryTime" value="<?php echo e(old('expiryTime')); ?>" required>
            </div>
        </div>

        <div class="import-method-selector">
            <div class="method-option" onclick="document.getElementById('method-manual').click()">
                <input type="radio" id="method-manual" name="creation_method" value="manual" checked>
                <label for="method-manual">
                    <strong>Type Manually</strong>
                    <span>Build questions individually below</span>
                </label>
            </div>
            <div class="method-option" onclick="document.getElementById('method-csv').click()">
                <input type="radio" id="method-csv" name="creation_method" value="csv">
                <label for="method-csv">
                    <strong>Bulk Import (.CSV)</strong>
                    <span>Upload & filter large question pool</span>
                </label>
            </div>
        </div>

        <div id="manual-questions-panel">
            <div class="section-title">
                <span>Quiz Questions</span>
                <button type="button" class="btn-add" id="add-question-btn">+ Add Question</button>
            </div>
            <div id="questions-wrapper"></div>
        </div>

        <div id="csv-questions-panel" class="csv-upload-panel">
            <label style="color: #ea580c; font-weight: 700;">📥 Select Question Spreadsheet Bank</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv" style="background: white; margin-top: 6px;">
            
            <div class="form-group" style="margin-top: 15px; display: none;" id="target-count-wrapper">
                <label for="select_count" style="color: #0b2265;">🎯 Number of Questions to Selection-Pull from Pool:</label>
                <input type="number" id="select_count" name="select_count" min="1" placeholder="e.g., 20">
                <span id="pool-info" style="font-size: 12px; color: #64748b; margin-top: 4px; display: block;"></span>
            </div>

            <div id="live-review-box" class="preview-box">
                <h4 style="font-size: 14px; margin-bottom: 8px; color: #1e293b;">📋 Live Preview Panel (First 5 Rows Detected):</h4>
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Question Text</th>
                            <th>A</th>
                            <th>B</th>
                            <th>C</th>
                            <th>D</th>
                            <th>Key</th>
                        </tr>
                    </thead>
                    <tbody id="preview-table-body"></tbody>
                </table>
            </div>
        </div>

        <input type="hidden" name="filtered_questions_json" id="filtered_questions_json">

        <button type="submit" class="btn-submit">Publish Entire Quiz</button>
    </form>
</div>

<script>
    const manualRadio = document.getElementById('method-manual');
    const csvRadio = document.getElementById('method-csv');
    const manualPanel = document.getElementById('manual-questions-panel');
    const csvPanel = document.getElementById('csv-questions-panel');
    const csvFileInput = document.getElementById('csv_file');
    const targetCountWrapper = document.getElementById('target-count-wrapper');
    const selectCountInput = document.getElementById('select_count');
    const poolInfo = document.getElementById('pool-info');
    const liveReviewBox = document.getElementById('live-review-box');
    const previewTableBody = document.getElementById('preview-table-body');
    const filteredJsonInput = document.getElementById('filtered_questions_json');

    let parsedQuestionsPool = [];

    function togglePanels() {
        if (manualRadio.checked) {
            manualPanel.style.display = 'block';
            csvPanel.style.display = 'none';
            csvFileInput.required = false;
            selectCountInput.required = false;
            toggleManualFieldsRequired(true);
        } else {
            manualPanel.style.display = 'none';
            csvPanel.style.display = 'block';
            csvFileInput.required = (parsedQuestionsPool.length === 0);
            selectCountInput.required = true;
            toggleManualFieldsRequired(false);
        }
    }

    function toggleManualFieldsRequired(isRequired) {
        const fields = manualPanel.querySelectorAll('textarea, input, select');
        fields.forEach(field => field.required = isRequired);
    }

    manualRadio.addEventListener('change', togglePanels);
    csvRadio.addEventListener('change', togglePanels);

    // 🔬 Local FileReader Engine for immediate live verification
    csvFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(evt) {
            const text = evt.target.result;
            const lines = text.split('\n');
            parsedQuestionsPool = [];
            previewTableBody.innerHTML = '';

            // Loop and drop blank header descriptors safely
            for (let i = 1; i < lines.length; i++) {
                if (!lines[i].trim()) continue;
                
                // Splitting parameters cleanly
                const columns = lines[i].split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
                if (columns.length >= 6) {
                    const questionObj = {
                        text: columns[0].replace(/^"|"$/g, '').trim(),
                        a: columns[1].replace(/^"|"$/g, '').trim(),
                        b: columns[2].replace(/^"|"$/g, '').trim(),
                        c: columns[3].replace(/^"|"$/g, '').trim(),
                        d: columns[4].replace(/^"|"$/g, '').trim(),
                        correct: columns[5].replace(/^"|"$/g, '').trim()
                    };
                    parsedQuestionsPool.push(questionObj);

                    // Append first 5 lines into Review Frame layout context
                    if (parsedQuestionsPool.length <= 5) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${questionObj.text}</td>
                            <td>${questionObj.a}</td>
                            <td>${questionObj.b}</td>
                            <td>${questionObj.c}</td>
                            <td>${questionObj.d}</td>
                            <td style="font-weight:700;color:#16a34a;">${questionObj.correct}</td>
                        `;
                        previewTableBody.appendChild(tr);
                    }
                }
            }

            if (parsedQuestionsPool.length > 0) {
                poolInfo.innerText = `Total items detected in uploaded file pool: ${parsedQuestionsPool.length} questions.`;
                selectCountInput.max = parsedQuestionsPool.length;
                selectCountInput.value = Math.min(20, parsedQuestionsPool.length); // Default target fallback preview
                targetCountWrapper.style.display = 'block';
                liveReviewBox.style.display = 'block';
            }
        };
        reader.readAsText(file);
    });

    // Intercept form submittal framework execution parameters
    document.getElementById('quiz-form').addEventListener('submit', function(e) {
        if (csvRadio.checked) {
            const targetCount = parseInt(selectCountInput.value);
            if (isNaN(targetCount) || targetCount < 1 || targetCount > parsedQuestionsPool.length) {
                alert(`Please declare a valid selection integer mapping bounded within total file limits (1-${parsedQuestionsPool.length}).`);
                e.preventDefault();
                return;
            }

            // Shuffle pool and sample exact numeric parameters target
            const shuffled = [...parsedQuestionsPool].sort(() => 0.5 - Math.random());
            const selectedSubset = shuffled.slice(0, targetCount);

            // Pass down to hidden field interface
            filteredJsonInput.value = JSON.stringify(selectedSubset);
        }
    });

    // RE-INDEX MANUALLY GENERATED FIELDS
    function reindexQuestions() {
        const wrapper = document.getElementById('questions-wrapper');
        const cards = wrapper.getElementsByClassName('question-card');
        Array.from(cards).forEach((card, index) => {
            card.querySelector('.question-num-label').innerText = `Question ${index + 1} Text`;
            card.querySelector('.question-text-field').name = `questions[${index}][text]`;
            card.querySelector('.opt-a-field').name = `questions[${index}][a]`;
            card.querySelector('.opt-b-field').name = `questions[${index}][b]`;
            card.querySelector('.opt-c-field').name = `questions[${index}][c]`;
            card.querySelector('.opt-d-field').name = `questions[${index}][d]`;
            card.querySelector('.correct-field').name = `questions[${index}][correct]`;
        });
    }

    document.getElementById('add-question-btn').addEventListener('click', function() {
        const wrapper = document.getElementById('questions-wrapper');
        const cardHtml = `
            <div class="question-card">
                <div class="form-group">
                    <label class="question-num-label">Question Text</label>
                    <textarea class="question-text-field" rows="2" required placeholder="Type question..."></textarea>
                </div>
                <div class="options-grid">
                    <div class="form-group"><label>Option A</label><input type="text" class="opt-a-field" required></div>
                    <div class="form-group"><label>Option B</label><input type="text" class="opt-b-field" required></div>
                    <div class="form-group"><label>Option C</label><input type="text" class="opt-c-field" required></div>
                    <div class="form-group"><label>Option D</label><input type="text" class="opt-d-field" required></div>
                </div>
                <div class="form-group" style="margin-top: 10px;">
                    <label style="color: #0b2265;">Correct Answer</label>
                    <select class="correct-field" required>
                        <option value="">-- Choose --</option>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
                <button type="button" class="btn-remove">Remove Question</button>
                <div style="clear: both;"></div>
            </div>`;
        wrapper.insertAdjacentHTML('beforeend', cardHtml);
        reindexQuestions();
        const newCard = wrapper.lastElementChild;
        newCard.querySelector('.btn-remove').addEventListener('click', function() {
            if(wrapper.getElementsByClassName('question-card').length > 1) {
                newCard.remove();
                reindexQuestions();
            }
        });
    });

    document.getElementById('add-question-btn').click();
</script>
</body>
</html><?php /**PATH C:\xampp\xam\htdocs\smart-discussion-forum\resources\views/quizzes/create.blade.php ENDPATH**/ ?>
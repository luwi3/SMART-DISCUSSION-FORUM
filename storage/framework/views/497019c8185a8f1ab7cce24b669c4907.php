<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: #f4f6f9; padding: 40px 20px; display: flex; justify-content: center; }
        .form-container { max-width: 750px; width: 100%; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; }
        .header h2 { color: #1e293b; font-size: 22px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #475569; }
        input[type="text"], input[type="number"], input[type="datetime-local"], select, textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #334155; outline: none; }
        input:focus, textarea:focus, select:focus { border-color: #0b2265; box-shadow: 0 0 0 3px rgba(11, 34, 101, 0.1); }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        /* Questions Styling */
        .section-title { font-size: 18px; color: #1e293b; margin: 30px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .question-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 20px; position: relative; }
        .btn-add { background: #10b981; color: white; border: none; padding: 8px 14px; font-weight: 600; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .btn-add:hover { background: #059669; }
        .btn-remove { background: #ef4444; color: white; border: none; padding: 6px 12px; font-weight: 600; border-radius: 4px; cursor: pointer; font-size: 12px; margin-top: 10px; float: right; }
        .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
        
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

    <form method="POST" action="<?php echo e(route('quizzes.store')); ?>">
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

        <div class="section-title">
            <span>Quiz Questions</span>
            <button type="button" class="btn-add" id="add-question-btn">+ Add Question</button>
        </div>

        <div id="questions-wrapper">
            <!-- Questions are dynamically generated by JavaScript to preserve perfectly sequential array indexing mapping -->
        </div>

        <button type="submit" class="btn-submit">Publish Entire Quiz</button>
    </form>
</div>

<script>
    // 🛠️ FIX: Re-index cards dynamically to prevent validation breaks if a user drops an intermediate field element card
    function reindexQuestions() {
        const wrapper = document.getElementById('questions-wrapper');
        const cards = wrapper.getElementsByClassName('question-card');
        
        Array.from(cards).forEach((card, index) => {
            // Update Title Label Context Text
            card.querySelector('.question-num-label').innerText = `Question ${index + 1} Text`;
            
            // Reassign HTML Array name references sequentially
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
                    <textarea class="question-text-field" rows="2" required placeholder="Type the question here..."></textarea>
                </div>
                <div class="options-grid">
                    <div class="form-group">
                        <label>Option A</label>
                        <input type="text" class="opt-a-field" required placeholder="Option A">
                    </div>
                    <div class="form-group">
                        <label>Option B</label>
                        <input type="text" class="opt-b-field" required placeholder="Option B">
                    </div>
                    <div class="form-group">
                        <label>Option C</label>
                        <input type="text" class="opt-c-field" required placeholder="Option C">
                    </div>
                    <div class="form-group">
                        <label>Option D</label>
                        <input type="text" class="opt-d-field" required placeholder="Option D">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 10px;">
                    <label style="color: #0b2265;">Correct Answer Option</label>
                    <select class="correct-field" required>
                        <option value="">-- Choose Key --</option>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>
                <button type="button" class="btn-remove">Remove Question</button>
                <div style="clear: both;"></div>
            </div>
        `;
        
        wrapper.insertAdjacentHTML('beforeend', cardHtml);
        reindexQuestions();
        
        // Setup listener on the newly created remove button element natively
        const newCard = wrapper.lastElementChild;
        newCard.querySelector('.btn-remove').addEventListener('click', function() {
            newCard.remove();
            reindexQuestions(); // Re-adjust immediately following node drop operations
        });
    });

    // Automatically generate the initial first question field upon page load initialization
    document.getElementById('add-question-btn').click();
</script>

</body>
</html><?php /**PATH C:\Users\DELL\OneDrive\Desktop\Herd\Smart-Discussion-App\resources\views/quizzes/create.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Submissions & Marks Management</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f4f7fc; padding: 40px; color: #334155; }
        
        .header-container { max-width: 1100px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center; }
        .back-btn { color: #2563eb; text-decoration: none; font-size: 14px; font-weight: 600; }
        .back-btn:hover { text-decoration: underline; }

        /* Full-width container since left sidebar is removed */
        .workspace-container { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px; }
        
        /* Individual Quiz block matching your paper sketch layout */
        .quiz-evaluation-block { background: white; padding: 28px; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
        
        /* Title metadata header section */
        .meta-header-info { margin-bottom: 20px; border-bottom: 2px dashed #e2e8f0; padding-bottom: 12px; }
        .quiz-main-title { font-size: 20px; font-weight: 700; color: #0b2265; }
        .quiz-sub-badge { font-size: 14px; color: #64748b; margin-top: 4px; font-weight: 500; }

        /* Data table matching your sketch dimensions */
        .evaluation-table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 10px; }
        .evaluation-table th, .evaluation-table td { padding: 14px 16px; border: 1px solid #cbd5e1; font-size: 14px; }
        
        .evaluation-table th { background-color: #f8fafc; color: #0f172a; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        .student-name-text { font-weight: 600; color: #1e293b; }
        .reg-no-text { font-family: 'Courier New', Courier, monospace; font-weight: 600; color: #475569; }
        .marks-obtained-data { font-weight: 700; color: #1e40af; }
        .submission-date-text { color: #64748b; font-size: 13px; }
        
        .no-records { padding: 20px; text-align: center; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>

    <div class="header-container">
        <a href="<?php echo e(route('lecturer.dashboard')); ?>" class="back-btn">← Back to Main Dashboard</a>
    </div>

    <main class="workspace-container">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($groupedQuizzes) && $groupedQuizzes->count() > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groupedQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quizID => $submissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php 
                    // Retrieve structural metadata parameters from the first dataset row instance
                    $firstSubmission = $submissions->first(); 
                ?>
                
                <section class="quiz-evaluation-block">
                    
                    <div class="meta-header-info">
                        <div class="quiz-main-title">Title: <?php echo e($firstSubmission->quiz_title); ?></div>
                        <div class="quiz-sub-badge">group: <?php echo e($firstSubmission->courseCode ?? 'BSSE'); ?> • Submissions: <?php echo e($submissions->count()); ?></div>
                    </div>

                    <table class="evaluation-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Student Name</th>
                                <th style="width: 20%;">Reg No</th>
                                <th style="width: 30%;">Marks Obtained</th>
                                <th style="width: 25%;">Submission Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <td class="student-name-text">
                                        <?php echo e($submission->student_name); ?>

                                    </td>
                                    
                                    <td class="reg-no-text">
                                        <?php echo e($submission->student_reg); ?>

                                    </td>
                                    
                                    <td class="marks-obtained-data">
                                        <?php echo e($submission->score); ?> / <?php echo e($submission->total_questions ?? 2); ?> = 
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($submission->total_questions ?? 2) > 0): ?>
                                            <?php echo e(round(($submission->score / $submission->total_questions) * 100)); ?>%
                                        <?php else: ?>
                                            0%
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    
                                    <td class="submission-date-text">
                                        <?php echo e(\Carbon\Carbon::parse($submission->timeSubmitted)->format('d M Y, h:i A')); ?>

                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>

                </section>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php else: ?>
            <div class="quiz-evaluation-block" style="text-align: center; padding: 40px; color: #64748b;">
                <h3>No active student submissions or evaluation logs recorded in the system.</h3>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </main>

</body>
</html><?php /**PATH C:\Users\dell\Herd\first-app\resources\views/quizzes/index.blade.php ENDPATH**/ ?>
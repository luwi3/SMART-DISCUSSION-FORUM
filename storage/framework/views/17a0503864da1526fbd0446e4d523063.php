<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Marks - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
        
        .top-brand-bar { background-color: #0c2340; color: white; padding: 18px 30px; font-weight: 700; font-size: 16px; letter-spacing: 0.5px; }
        .workspace-layout { display: flex; flex-grow: 1; }

        /* Sidebar Styling (Matches Dashboard) */
        .sidebar { width: 260px; background-color: #0a1931; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-menu { list-style: none; padding: 24px 0; }
        .menu-item a { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; }
        .menu-item.active a { color: white; background: #2563eb; }
        .badge { background: #ef4444; color: white; font-size: 11px; padding: 2px 7px; border-radius: 20px; font-weight: 700; }
        .logout-btn { width: 100%; background: none; border: none; padding: 14px 24px; color: #f43f5e; text-align: left; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); }

        /* Main Marks Content Area */
        .main-content { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; gap: 30px; }
        .content-panel { background: white; border-radius: 12px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); max-width: 1100px; }
        
        /* Table Layout */
        .list-row-item { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; }
        .list-row-item:last-child { margin-bottom: 0; }
        .item-info-meta { display: flex; flex-direction: column; gap: 4px; }
        .item-info-title { font-size: 15px; font-weight: 700; color: #1e293b; }
        .item-info-badge { font-size: 12px; color: #64748b; background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-weight: 600; width: fit-content; }
        
        .grade-display { text-align: right; }
        .grade-score { font-size: 18px; font-weight: 800; color: #2563eb; }
        .grade-total { font-size: 13px; color: #64748b; font-weight: 500; }
        .grade-time { font-size: 12px; color: #94a3b8; margin-top: 4px; display: block; }
    </style>
</head>
<body>

    <div class="top-brand-bar">SMART DISCUSSION FORUM</div>

    <div class="workspace-layout">
        
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item"><a href="<?php echo e(route('student.dashboard')); ?>">Profile</a></li>
                <li class="menu-item active"><a href="#">Marks</a></li>
                <li class="menu-item"><a href="<?php echo e(route('chat.index')); ?>">Chats</a></li>
                <li class="menu-item"><a href="#">Notifications <span class="badge">3</span></a></li>
                <li class="menu-item"><a href="#">Announcements</a></li>
            </ul>
            <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>
        
        <main class="main-content">
            <section class="content-panel">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 16px;">
                    <span style="font-size: 24px;">🏆</span>
                    <div>
                        <h2 style="font-size: 20px; font-weight: 700; color: #0f172a;">My Quiz Performance Ledger</h2>
                        <p style="font-size: 14px; color: #64748b;">Track your official assessment results and score summaries</p>
                    </div>
                </div>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grades->isEmpty()): ?>
                    <div style="text-align: center; padding: 50px 20px; color: #64748b; background: #f8fafc; border-radius: 8px; border: 2px dashed #cbd5e1;">
                        <p style="font-size: 15px; font-weight: 600; color: #334155;">No evaluation logs found</p>
                        <p style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Once you complete an active course quiz, your grading records will show up here.</p>
                    </div>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="list-row-item">
                            <div class="item-info-meta">
                                <span class="item-info-title"><?php echo e($grade->quiz_title); ?></span>
                                <span class="item-info-badge"><?php echo e($grade->courseCode); ?></span>
                            </div>
                            <div class="grade-display">
                                <span class="grade-score"><?php echo e($grade->score); ?></span>
                                <span class="grade-total">/ <?php echo e($grade->total_questions); ?></span>
                                <span class="grade-time">
                                    <?php echo e(\Carbon\Carbon::parse($grade->timeSubmitted)->format('M d, Y @ h:i A')); ?>

                                </span>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        </main>

    </div>

</body>
</html><?php /**PATH C:\Users\dell\Herd\first-app\resources\views/quizzes/student_marks.blade.php ENDPATH**/ ?>
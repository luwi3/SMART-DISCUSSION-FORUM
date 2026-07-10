<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
        
        .top-brand-bar { background-color: #0c2340; color: white; padding: 18px 30px; font-weight: 700; font-size: 16px; letter-spacing: 0.5px; }
        .workspace-layout { display: flex; flex-grow: 1; }

        /* Left Navbar */
        .sidebar { width: 260px; background-color: #0a1931; color: white; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-menu { list-style: none; padding: 24px 0; }
        .menu-item a { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 600; }
        .menu-item.active a { color: white; background: #2563eb; }
        .badge { background: #ef4444; color: white; font-size: 11px; padding: 2px 7px; border-radius: 20px; font-weight: 700; }
        .logout-btn { width: 100%; background: none; border: none; padding: 14px 24px; color: #f43f5e; text-align: left; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); }

        /* Dashboard Container */
        .main-content { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; gap: 30px; }
        .welcome-header { margin-bottom: -10px; }
        .welcome-txt { font-size: 26px; font-weight: 700; color: #0f172a; }
        .welcome-sub { font-size: 14px; color: #64748b; margin-top: 4px; }

        /* Notification Banner Component */
        .alert-banner { background-color: #eff6ff; border-left: 4px solid #2563eb; color: #1e3a8a; padding: 16px 20px; border-radius: 8px; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: flex-start; gap: 12px; }
        .alert-icon { font-size: 18px; line-height: 1; }
        .alert-heading { margin: 0; font-weight: 700; color: #1e293b; }
        .alert-body { margin: 4px 0 0 0; color: #2563eb; font-weight: 600; }

        /* Top Grid Metrics */
        .cards-row { display: flex; gap: 20px; flex-wrap: wrap; }
        .metric-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; width: 220px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .m-title { font-size: 13px; font-weight: 700; color: #16a34a; margin-bottom: 12px; }
        .m-val { font-size: 32px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .m-sub { font-size: 12px; color: #64748b; }
        .progress-line { width: 100%; height: 6px; background: #e2e8f0; border-radius: 4px; margin-top: 10px; overflow: hidden; }
        .progress-fill { background: #16a34a; height: 100%; width: 85%; }

        .status-check { width: 36px; height: 36px; background: #dcfce7; color: #15803d; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 10px 0; }
        .btn-topic-action { background: #1d4ed8; color: white; border: none; width: 100%; padding: 10px; font-weight: 700; border-radius: 6px; font-size: 12px; cursor: pointer; margin-top: 12px; text-align: center; text-decoration: none; display: block; }

        /* Main Workspace Split Layout */
        .dashboard-grid { display: flex; gap: 24px; flex-wrap: wrap; align-items: flex-start; }
        .left-column { flex: 1; min-width: 400px; display: flex; flex-direction: column; gap: 24px; }
        .right-column { width: 400px; display: flex; flex-direction: column; gap: 24px; }

        /* Feed Timelines & Blocks */
        .content-panel { background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .panel-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .feed-list { display: flex; flex-direction: column; gap: 18px; }
        .feed-item { display: flex; gap: 14px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
        .feed-item:last-child { border: none; padding-bottom: 0; }
        .feed-avatar { width: 32px; height: 32px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #2563eb; font-size: 13px; }
        .feed-msg-title { font-size: 13px; font-weight: 700; color: #334155; }
        .feed-time { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .btn-view-all { width: 100%; background: #1d4ed8; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; margin-top: 20px; }

        /* List Items styling */
        .list-row-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; }
        .list-row-item:last-child { margin-bottom: 0; }
        .item-info-meta { display: flex; flex-direction: column; gap: 4px; }
        .item-info-title { font-size: 14px; font-weight: 700; color: #334155; }
        .item-info-badge { font-size: 11px; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; width: fit-content; }
        
        /* Grade Score Tags */
        .grade-display { text-align: right; }
        .grade-score { font-size: 16px; font-weight: 800; color: #0f172a; }
        .grade-total { font-size: 12px; color: #64748b; font-weight: 500; }
        .grade-status-passed { color: #16a34a; font-size: 11px; font-weight: 700; display: block; margin-top: 2px; }
        .grade-status-failed { color: #dc2626; font-size: 11px; font-weight: 700; display: block; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="top-brand-bar">SMART DISCUSSION FORUM</div>
    <div class="workspace-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item active"><a href="#">Profile</a></li>
                <li class="menu-item"><a href="#grades-section">Marks</a></li>
                <li class="menu-item"><a href="<?php echo e(route('chat.index')); ?>">Chats</a></li>
                <li class="menu-item"><a href="#">Notifications <span class="badge">3</span></a></li>
                <li class="menu-item"><a href="#">Announcements</a></li>
            </ul>
            <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>
        
        <main class="main-content">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('quiz_result')): ?>
                <div class="alert-banner">
                    <span class="alert-icon">🔔</span>
                    <div>
                        <h4 class="alert-heading">Evaluation Center Update</h4>
                        <p class="alert-body"><?php echo e(session('quiz_result')); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div class="alert-banner" style="background-color: #fef2f2; border-left-color: #ef4444; color: #991b1b;">
                    <span class="alert-icon">⚠️</span>
                    <div>
                        <h4 class="alert-heading" style="color: #991b1b;">System Notice</h4>
                        <p class="alert-body" style="color: #ef4444;"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="welcome-header">
                <h1 class="welcome-txt">Hello, <?php echo e(Auth::user()->name); ?>! 👋</h1>
                <p class="welcome-sub">Always keep learning and stay active.</p>
            </div>

            <section class="cards-row">
                <div class="metric-card">
                    <div class="m-title">Participation Marks</div>
                    <div class="m-val">85%</div>
                    <div class="m-sub">Great Job! 🚀</div>
                    <div class="progress-line"><div class="progress-fill"></div></div>
                </div>
                <div class="metric-card" style="width:160px;">
                    <div class="m-title" style="color:#1e293b;">Status</div>
                    <div class="status-check">✓</div>
                    <div class="m-sub">Account Active</div>
                </div>
                <div class="metric-card" style="width:240px;">
                    <div class="m-title" style="color:#7c3aed;">Recommended Topic</div>
                    <div class="m-val" style="font-size:18px; margin-top:10px; margin-bottom:15px;">Database Design</div>
                    <a href="#" class="btn-topic-action">VIEW TOPICS</a>
                </div>
            </section>

            <!-- Main Split Layout Grid -->
            <div class="dashboard-grid">
                
                <!-- Left Side: Assessments and Marks View -->
                <div class="left-column">
                    
                    <!-- 📊 Assessment Grades & Marks Section -->
                    <section id="grades-section" class="content-panel">
                        <h3 class="panel-title">📊 Assessment Performance & Marks</h3>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($completedQuizzes) && count($completedQuizzes) > 0): ?>
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">Review your scores and feedback from completed assessments.</p>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $completedQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="list-row-item">
                                    <div class="item-info-meta">
                                        <span class="item-info-title"><?php echo e($quiz->title); ?></span>
                                        <span class="item-info-badge"><?php echo e($quiz->courseCode ?? 'Course'); ?> • Completed <?php echo e(\Carbon\Carbon::parse($quiz->pivot->updated_at ?? now())->diffForHumans()); ?></span>
                                    </div>
                                    <div class="grade-display">
                                        <span class="grade-score"><?php echo e($quiz->pivot->score ?? $quiz->score); ?></span>
                                        <span class="grade-total">/ <?php echo e($quiz->total_marks ?? 100); ?></span>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($quiz->pivot->score ?? $quiz->score) >= ($quiz->passing_marks ?? 50)): ?>
                                            <span class="grade-status-passed">Passed ✓</span>
                                        <?php else: ?>
                                            <span class="grade-status-failed">Failed ✕</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php else: ?>
                            <!-- Fallback if no quizzes have been evaluated yet -->
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">No completed evaluation results are registered to your gradebook yet.</p>
                            <div class="list-row-item" style="opacity: 0.6; background: #f1f5f9; justify-content: center; padding: 20px;">
                                <span style="color: #94a3b8; font-size: 13px; font-weight: 600;">No Academic Transcripts Formed</span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>

                    <!-- ✍️ Available Assessments Block -->
                    <section class="content-panel">
                        <h3 class="panel-title">✍️ Available Assessments</h3>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($activeQuizzes) && count($activeQuizzes) > 0): ?>
                            <p style="color: #10b981; font-size: 14px; margin-bottom: 15px; font-weight: 600;">✅ Your registered course streams have active evaluation windows open.</p>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activeQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activeQuiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="list-row-item">
                                    <div class="item-info-meta">
                                        <span class="item-info-title"><?php echo e($activeQuiz->title); ?></span>
                                        <span class="item-info-badge"><?php echo e($activeQuiz->courseCode); ?> • <?php echo e($activeQuiz->duration); ?> Mins</span>
                                    </div>
                                    <a href="<?php echo e(route('quizzes.show', ['quizID' => $activeQuiz->quizID ?? $activeQuiz->id])); ?>" style="display: inline-block; padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
                                        ✍️ Attempt Quiz
                                    </a>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php else: ?>
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">No active evaluation windows are currently open for your course stream.</p>
                            <div class="list-row-item" style="opacity: 0.6; background: #f1f5f9;">
                                <div class="item-info-meta">
                                    <span class="item-info-title" style="color: #94a3b8;">No Evaluation Scheduled</span>
                                    <span class="item-info-badge" style="background: #cbd5e1; color: #64748b;">-- • 0 Mins</span>
                                </div>
                                <button disabled style="display: inline-block; padding: 8px 16px; background: #94a3b8; color: #e2e8f0; border-radius: 6px; border: none; font-weight: bold; font-size: 13px; cursor: not-allowed;">
                                    🔒 Attempt Quiz
                                </button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>
                </div>

                <!-- Right Side: Recent Announcements -->
                <div class="right-column">
                    <section class="content-panel">
                        <div class="panel-title">📢 Recent Announcements</div>
                        <div class="feed-list">
                            <div class="feed-item">
                                <div class="feed-avatar">D</div>
                                <div><div class="feed-msg-title">New quiz available: Software Engineering</div><div class="feed-time">2 hours ago</div></div>
                            </div>
                            <div class="feed-item">
                                <div class="feed-avatar">D</div>
                                <div><div class="feed-msg-title">Department meeting on Friday</div><div class="feed-time">1 day ago</div></div>
                            </div>
                            <div class="feed-item">
                                <div class="feed-avatar">D</div>
                                <div><div class="feed-msg-title">Submit your group project</div><div class="feed-time">2 days ago</div></div>
                            </div>
                        </div>
                        <button class="btn-view-all">VIEW ALL</button>
                    </section>
                </div>

            </div>
        </main>
    </div>
</body>
</html><?php /**PATH C:\Users\dell\Herd\first-app\resources\views/dashboards/student.blade.php ENDPATH**/ ?>
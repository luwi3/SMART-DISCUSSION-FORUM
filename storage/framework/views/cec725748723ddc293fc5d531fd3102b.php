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
        .menu-item a:hover { color: white; background: rgba(255, 255, 255, 0.05); }
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
        .metric-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; width: 240px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .m-title { font-size: 13px; font-weight: 700; color: #2563eb; margin-bottom: 12px; }
        .m-val { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .m-sub { font-size: 12px; color: #64748b; }
        .progress-line { width: 100%; height: 6px; background: #e2e8f0; border-radius: 4px; margin-top: 10px; overflow: hidden; }
        .progress-fill { background: #2563eb; height: 100%; }

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
        .btn-view-all { width: 100%; background: #1d4ed8; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; margin-top: 20px; text-decoration: none; text-align: center; display: block; }

        /* List Items styling */
        .list-row-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; }
        .list-row-item:last-child { margin-bottom: 0; }
        .item-info-meta { display: flex; flex-direction: column; gap: 4px; }
        .item-info-title { font-size: 14px; font-weight: 700; color: #334155; }
        .item-info-badge { font-size: 11px; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; width: fit-content; }

        /* Student Details Card Grid */
        .details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 15px; }
        .detail-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; }
        .detail-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
        .detail-value { font-size: 15px; font-weight: 700; color: #0f172a; }
    </style>
</head>
<body>
    <div class="top-brand-bar">SMART DISCUSSION FORUM</div>
    <div class="workspace-layout">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="menu-item <?php echo e((!isset($currentTab) || $currentTab === 'main') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('student.dashboard', ['tab' => 'main'])); ?>">Main Menu</a>
                </li>
                <li class="menu-item <?php echo e((isset($currentTab) && $currentTab === 'profile') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('student.dashboard', ['tab' => 'profile'])); ?>">Profile</a>
                </li>
                <li class="menu-item"><a href="<?php echo e(route('student.marks')); ?>">Marks</a></li>
                <li class="menu-item"><a href="<?php echo e(route('chat.index')); ?>">Chats</a></li>
                <li class="menu-item"><a href="#">Notifications <span class="badge">3</span></a></li>
                <li class="menu-item <?php echo e((isset($currentTab) && $currentTab === 'announcements') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('student.dashboard', ['tab' => 'announcements'])); ?>">Announcements</a>
                </li>
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($currentTab) && $currentTab === 'profile'): ?>
                <!-- 👤 STUDENT DETAILS / PROFILE VIEW -->
                <div class="content-panel">
                    <div class="panel-title" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>👤 Student Profile & Account Details</span>
                        <a href="<?php echo e(route('student.dashboard', ['tab' => 'main'])); ?>" style="font-size: 13px; background: #e2e8f0; color: #334155; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 600;">← Main Menu</a>
                    </div>
                    
                    <div class="details-grid">
                        <div class="detail-box">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value"><?php echo e(Auth::user()->name); ?></div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Email Address</div>
                            <div class="detail-value"><?php echo e(Auth::user()->email); ?></div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Account Role</div>
                            <div class="detail-value" style="text-transform: capitalize;"><?php echo e(Auth::user()->role ?? 'Student'); ?></div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Registration / ID</div>
                            <div class="detail-value"><?php echo e(Auth::user()->regNo ?? Auth::user()->id); ?></div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Account Status</div>
                            <div class="detail-value" style="color: #15803d;">Active</div>
                        </div>
                        <div class="detail-box">
                            <div class="detail-label">Member Since</div>
                            <div class="detail-value"><?php echo e(Auth::user()->created_at ? Auth::user()->created_at->format('M d, Y') : 'N/A'); ?></div>
                        </div>
                    </div>
                </div>

            <?php elseif(isset($currentTab) && $currentTab === 'announcements'): ?>
                <!-- 📢 FULL ANNOUNCEMENTS VIEW -->
                <div class="content-panel">
                    <div class="panel-title" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>📢 All Department Announcements</span>
                        <a href="<?php echo e(route('student.dashboard', ['tab' => 'main'])); ?>" style="font-size: 13px; background: #e2e8f0; color: #334155; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 600;">← Main Menu</a>
                    </div>
                    
                    <div class="feed-list" style="margin-top: 20px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($announcements) && count($announcements) > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="list-row-item" style="display: flex; flex-direction: column; align-items: flex-start; gap: 8px;">
                                    <div style="display: flex; justify-content: space-between; width: 100%;">
                                        <span class="item-info-title" style="font-size: 16px;"><?php echo e($announcement->title); ?></span>
                                        <span class="item-info-badge"><?php echo e($announcement->courseCode); ?></span>
                                    </div>
                                    <!-- UPDATED TO UNESCAPED HTML TO RENDER DOWNLOAD BUTTON PROPERLY -->
                                    <div style="font-size: 14px; color: #475569; line-height: 1.5; width: 100%;"><?php echo $announcement->message; ?></div>
                                    <span style="font-size: 11px; color: #94a3b8;">Posted <?php echo e($announcement->created_at ? $announcement->created_at->diffForHumans() : 'Recently'); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php else: ?>
                            <p style="color: #64748b; font-size: 14px;">No announcements found.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- 📊 MAIN MENU / DASHBOARD VIEW -->
                <section class="cards-row">
                    <div class="metric-card">
                        <div class="m-title">🤖 Forum Participation</div>
                        <div class="m-val">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($maxPossibleMarks) && $maxPossibleMarks > 0): ?>
                                <?php echo e($totalParticipationScore ?? 0); ?> <span style="font-size: 16px; color: #64748b; font-weight: 500;">/ <?php echo e($maxPossibleMarks); ?></span>
                            <?php else: ?>
                                0 <span style="font-size: 14px; color: #94a3b8;">Marks</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="m-sub">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($maxPossibleMarks) && $maxPossibleMarks > 0): ?>
                                Live Contribution Progress
                            <?php else: ?>
                                No discussion active yet
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="progress-line">
                            <?php 
                                $percent = (isset($maxPossibleMarks) && $maxPossibleMarks > 0) ? min((($totalParticipationScore ?? 0) / $maxPossibleMarks) * 100, 100) : 0; 
                            ?>
                            <div class="progress-fill" style="width: <?php echo e($percent); ?>%;"></div>
                        </div>
                    </div>

                    <div class="metric-card" style="width:160px;">
                        <div class="m-title" style="color:#1e293b;">Status</div>
                        <div class="status-check">✓</div>
                        <div class="m-sub">Account Active</div>
                    </div>
                    
                    <div class="metric-card" style="width:240px;">
                        <div class="m-title" style="color:#7c3aed;">Recommended Topic</div>
                        <div class="m-val" style="font-size:18px; margin-top:10px; margin-bottom:15px;">Database Design</div>
                        <a href="<?php echo e(route('chat.index')); ?>" class="btn-topic-action">VIEW TOPICS</a>
                    </div>
                </section>

                <div class="dashboard-grid">
                    
                    <div class="left-column">
                        <section class="content-panel">
                            <h3 class="panel-title">✍️ Available Assessments</h3>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($activeQuizzes) && count($activeQuizzes) > 0): ?>
                                <p style="color: #10b981; font-size: 14px; margin-bottom: 15px; font-weight: 600;">✅ Your registered course streams have active evaluation windows open.</p>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activeQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activeQuiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $currentQuizId = $activeQuiz->quizID ?? $activeQuiz->id;
                                        
                                        $hasCompleted = isset($completedQuizzes) && $completedQuizzes->contains(function($completed) use ($currentQuizId) {
                                            return ($completed->quizID ?? $completed->id) == $currentQuizId;
                                        });
                                    ?>

                                    <div class="list-row-item">
                                        <div class="item-info-meta">
                                            <span class="item-info-title"><?php echo e($activeQuiz->title); ?></span>
                                            <span class="item-info-badge"><?php echo e($activeQuiz->courseCode); ?> • <?php echo e($activeQuiz->duration); ?> Mins</span>
                                        </div>
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCompleted): ?>
                                            <span style="display: inline-block; padding: 8px 16px; background: #e2e8f0; color: #475569; border-radius: 6px; font-weight: bold; font-size: 13px; border: 1px solid #cbd5e1;">
                                                ✓ Completed
                                            </span>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('quizzes.show', ['quizID' => $currentQuizId])); ?>" style="display: inline-block; padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px;">
                                                ✍️ Attempt Quiz
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

                    <div class="right-column">
                        <section class="content-panel">
                            <div class="panel-title">📢 Recent Announcements</div>
                            <div class="feed-list">
                                <?php
                                    $sidebarAnnouncements = \App\Models\Announcement::latest()->take(3)->get();
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sidebarAnnouncements->count() > 0): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="feed-item">
                                            <div class="feed-avatar"><?php echo e(strtoupper(substr($announcement->courseCode ?? 'D', 0, 1))); ?></div>
                                            <div>
                                                <div class="feed-msg-title"><?php echo e($announcement->title); ?></div>
                                                <!-- OPTIONAL: Renders links/formatting nicely if needed in sidebar too -->
                                                <div style="font-size: 12px; color: #334155; margin-top: 2px;"><?php echo $announcement->message; ?></div>
                                                <div class="feed-time"><?php echo e($announcement->created_at ? $announcement->created_at->diffForHumans() : 'Recent'); ?></div>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php else: ?>
                                    <p style="color: #64748b; font-size: 13px;">No announcements posted yet.</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <a href="<?php echo e(route('student.dashboard', ['tab' => 'announcements'])); ?>" class="btn-view-all">VIEW ALL</a>
                        </section>
                    </div>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </main>
    </div>
</body>
</html><?php /**PATH C:\xampp\xam\htdocs\smart-discussion-forum\resources\views/dashboards/student.blade.php ENDPATH**/ ?>
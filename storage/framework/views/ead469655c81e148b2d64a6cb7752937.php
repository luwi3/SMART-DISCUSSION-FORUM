<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Topic - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f4f7fc; padding: 40px 20px; color: #334155; }
        .form-card { max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
        .form-heading { color: #0b2265; font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .form-desc { font-size: 14px; color: #64748b; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 14px; font-weight: 600; color: #0f172a; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        .form-control:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn-submit { padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; }
        .btn-submit:hover { background: #047857; }
        .back-btn { color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="form-card">
        <h2 class="form-heading">📝 Create Discussion Topic</h2>
        <p class="form-desc">Publish a new discussion focus window. Student engagements will be calculated right into your grades ledger.</p>

        <form action="<?php echo e(route('topics.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="form-label">Topic Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., Understanding Object-Oriented Principles" required>
            </div>

            <div class="form-group">
                <label class="form-label">Discussion Prompt / Instructions</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Provide background info or question tasks for students..." required></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 28px;">
                <a href="<?php echo e(route('lecturer.dashboard')); ?>" class="back-btn">Cancel</a>
                <button type="submit" class="btn-submit">Publish Topic</button>
            </div>
        </form>
    </div>

</body>
</html><?php /**PATH C:\Users\DELL\OneDrive\Desktop\Herd\Smart-Discussion-App\resources\views/topics/create.blade.php ENDPATH**/ ?>
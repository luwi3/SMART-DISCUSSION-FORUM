<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($topic->title); ?></title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { border-bottom: 2px solid #4A5568; padding-bottom: 10px; margin-bottom: 20px; }
        .topic-title { font-size: 24px; color: #2D3748; margin: 0; }
        .message-box { margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #E2E8F0; }
        .meta { font-size: 12px; color: #718096; margin-bottom: 5px; }
        .content { font-size: 14px; line-height: 1.5; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="topic-title"><?php echo e($topic->title); ?></h1>
        <p style="font-size: 12px; color: #718096;">Exported on: <?php echo e(now()->toDayDateTimeString()); ?></p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topic->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="message-box">
            <div class="meta">
                <strong><?php echo e($message->user->name ?? 'Unknown Member'); ?></strong> 
                • <?php echo e($message->created_at->format('M d, Y h:i A')); ?>

            </div>
            <div class="content">
                <?php echo e($message->body); ?>

            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

</body>
</html><?php /**PATH C:\Users\DELL\OneDrive\Desktop\Herd\Smart-Discussion-App\resources\views/pdf/topic-messages.blade.php ENDPATH**/ ?>
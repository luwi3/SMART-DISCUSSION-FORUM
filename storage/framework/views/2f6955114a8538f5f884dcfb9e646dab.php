<!DOCTYPE html>
<html>
<head>
    <title>Account Status Warning</title>
</head>
<body>
    <h1>Account Status Notice ⚠️</h1>
    <p>Dear <?php echo e($student->name); ?>,</p>
    
    <p>Your student account associated with registration number <strong><?php echo e($student->regNo); ?></strong> will be blocked in 24 hours if you do not engage in the discussions.</p>
    
    <p>Please log in to your dashboard and participate as soon as possible to keep your account active.</p>
    
    <hr>
    <p>Regards,<br>Administration Team</p>
</body>
</html><?php /**PATH C:\Users\dell\Herd\first-app\resources\views/emails/warning.blade.php ENDPATH**/ ?>
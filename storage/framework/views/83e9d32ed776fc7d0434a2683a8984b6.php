<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Login'); ?> | PoultryPro</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🥚</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Cabinet+Grotesk:wght@500;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Tailwind CDN for instant styling without npm run dev -->
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body class="relative flex min-h-screen items-center justify-center bg-slate-50 p-4 font-sans text-zinc-900 overflow-hidden selection:bg-emerald-500/30">
    <!-- Ambient Animated Background (Light Mode) -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] h-[70%] w-[50%] animate-[spin_20s_linear_infinite] rounded-full bg-emerald-300/40 blur-[100px]"></div>
        <div class="absolute top-[40%] -right-[10%] h-[60%] w-[40%] animate-[spin_25s_linear_infinite_reverse] rounded-full bg-sky-300/40 blur-[100px]"></div>
        <div class="absolute -bottom-[20%] left-[20%] h-[50%] w-[60%] animate-[spin_30s_linear_infinite] rounded-full bg-indigo-300/40 blur-[100px]"></div>
    </div>

    <!-- Content Wrapper -->
    <div class="relative z-10 w-full max-w-md">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/layouts/auth.blade.php ENDPATH**/ ?>
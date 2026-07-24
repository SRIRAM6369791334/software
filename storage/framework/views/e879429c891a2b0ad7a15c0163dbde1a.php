<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div id="login-container" class="opacity-0 translate-y-8 transition-all duration-1000 ease-out">
    <div class="mb-8 flex flex-col items-center justify-center gap-3">
        <div id="logo-icon" class="group relative flex h-16 w-16 cursor-pointer items-center justify-center rounded-3xl bg-gradient-to-tr from-emerald-500 to-sky-400 text-white shadow-lg shadow-emerald-500/30 transition-transform duration-300 hover:scale-110 hover:rotate-6">
            <span class="material-symbols-rounded text-4xl transition-transform duration-500 group-hover:-rotate-12 group-hover:scale-110">egg</span>
            <div class="absolute inset-0 rounded-3xl bg-white/40 opacity-0 blur-md transition-opacity duration-300 group-hover:opacity-100"></div>
        </div>
        <div class="text-center">
            <h1 class="text-3xl font-black tracking-tight text-zinc-900 drop-shadow-sm">PoultryPro</h1>
            <p class="text-sm font-medium tracking-wide text-zinc-500">Management System</p>
        </div>
    </div>

    <!-- Glassmorphism Card (Light Mode) -->
    <div class="relative overflow-hidden rounded-[2.5rem] border border-white/60 bg-white/70 p-8 shadow-xl shadow-zinc-200/50 backdrop-blur-2xl transition-all duration-300 hover:border-white hover:bg-white/80 hover:shadow-2xl hover:shadow-emerald-100/50">
        <!-- Inner subtle glow -->
        <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

        <h2 class="mb-2 text-2xl font-bold tracking-tight text-zinc-900">Welcome back</h2>
        <p class="mb-8 text-sm font-medium text-zinc-500">Sign in to your account</p>

        <?php if($errors->any()): ?>
            <div class="mb-6 animate-shake rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-700 backdrop-blur-md">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('login')); ?>" method="POST" class="relative z-10 space-y-5">
            <?php echo csrf_field(); ?>

            <div class="group">
                <label for="login" class="mb-1.5 block text-sm font-bold text-zinc-700 transition-colors group-focus-within:text-emerald-600">Email or Username</label>
                <div class="relative">
                    <input type="text" id="login" name="login" required autofocus
                           value="<?php echo e(old('login')); ?>"
                           placeholder="you@example.com"
                           class="peer w-full rounded-2xl border border-white/80 bg-white/60 px-5 py-3.5 text-sm font-semibold text-zinc-900 placeholder-zinc-400 shadow-inner backdrop-blur-md transition-all duration-300 focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10 hover:bg-white/80 <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                </div>
            </div>

            <div class="group">
                <label for="password" class="mb-1.5 block text-sm font-bold text-zinc-700 transition-colors group-focus-within:text-emerald-600">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="peer w-full rounded-2xl border border-white/80 bg-white/60 px-5 py-3.5 text-sm font-semibold text-zinc-900 placeholder-zinc-400 shadow-inner backdrop-blur-md transition-all duration-300 focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10 hover:bg-white/80">
                </div>
            </div>

            <!-- <div class="flex items-center gap-3 pt-2 pb-4">
                <div class="relative flex h-5 w-5 items-center justify-center">
                    <input type="checkbox" id="remember" name="remember" value="1"
                           class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-zinc-300 bg-white checked:border-emerald-500 checked:bg-emerald-500 transition-all hover:border-emerald-400">
                    <span class="material-symbols-rounded absolute pointer-events-none text-white opacity-0 text-[14px] font-bold peer-checked:opacity-100 transition-opacity">check</span>
                </div>
                <label for="remember" class="cursor-pointer text-sm font-medium text-zinc-600 transition-colors hover:text-zinc-900">Remember me</label>
            </div> -->

            <!-- Magnetic Button -->
            <button type="submit" id="magnetic-btn"
                    class="group relative flex w-full items-center justify-center overflow-hidden rounded-2xl bg-emerald-600 px-4 py-4 text-sm font-black text-white shadow-[0_8px_30px_-10px_rgba(16,185,129,0.5)] transition-all duration-300 hover:scale-[1.02] hover:bg-emerald-500 hover:shadow-[0_15px_40px_-15px_rgba(16,185,129,0.7)] active:scale-95">
                <span class="relative z-10 flex items-center gap-2">
                    Sign In
                    <span class="material-symbols-rounded text-lg transition-transform duration-300 group-hover:translate-x-1">arrow_forward</span>
                </span>
                <div class="absolute inset-0 z-0 bg-gradient-to-r from-emerald-400/0 via-white/30 to-emerald-400/0 opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-hover:animate-[shimmer_1.5s_infinite]"></div>
            </button>
        </form>
    </div>

    <p class="mt-8 text-center text-xs font-semibold tracking-wide text-zinc-400">PoultryPro v1.0 &copy; <?php echo e(date('Y')); ?></p>
</div>

<!-- Custom Scripts for Antigravity & Magic Spells -->
<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px) rotate(-1deg); }
        75% { transform: translateX(8px) rotate(1deg); }
    }
    .animate-shake {
        animation: shake 0.4s ease-in-out;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Entrance Animation
        gsap.to("#login-container", {
            opacity: 1,
            y: 0,
            duration: 1.2,
            ease: "elastic.out(1, 0.7)",
            delay: 0.1
        });

        // Magnetic Button Logic
        const btn = document.getElementById('magnetic-btn');
        
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            // Move the button slightly towards the cursor
            gsap.to(btn, {
                x: x * 0.15,
                y: y * 0.15,
                duration: 0.3,
                ease: "power2.out"
            });
        });

        btn.addEventListener('mouseleave', () => {
            // Snap back to center
            gsap.to(btn, {
                x: 0,
                y: 0,
                duration: 0.7,
                ease: "elastic.out(1, 0.3)"
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\auth\login.blade.php ENDPATH**/ ?>
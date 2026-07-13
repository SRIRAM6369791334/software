
<?php $__env->startSection('title', 'Expense vs Income'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-2">
    <a href="<?php echo e(route('profit.index')); ?>" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider inline-block">← Back to Overview</a>
</div>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Expense vs Income Matrix','subtitle' => 'Comparative study of business efficiency']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Expense vs Income Matrix','subtitle' => 'Comparative study of business efficiency']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('header', null, []); ?> 
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Income Breakdown (This Month)</h3>
         <?php $__env->endSlot(); ?>
        <div class="flex justify-center mb-10">
            <div class="relative w-48 h-48 rounded-full border-[16px] border-emerald-500 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-[10px] font-bold text-zinc-400 uppercase">Revenue</p>
                    <p class="text-xl font-black text-zinc-950">Rs <?php echo e(number_format($summary['revenue'], 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Scheduled Invoicing</span>
                <span class="font-bold text-zinc-950">65%</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Counter Sales</span>
                <span class="font-bold text-zinc-950">35%</span>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('header', null, []); ?> 
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Expense Matrix (Current)</h3>
         <?php $__env->endSlot(); ?>
        <div class="flex justify-center mb-10">
            <div class="relative w-48 h-48 rounded-full border-[16px] border-rose-500 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-[10px] font-bold text-zinc-400 uppercase">Outflow</p>
                    <p class="text-xl font-black text-zinc-950">Rs <?php echo e(number_format($summary['purchase'] + $summary['expenses'], 0)); ?></p>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Procurement (Stock)</span>
                <span class="font-bold text-zinc-950">Rs <?php echo e(number_format($summary['purchase'], 0)); ?></span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Operationals & EMIs</span>
                <span class="font-bold text-zinc-950">Rs <?php echo e(number_format($summary['expenses'], 0)); ?></span>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
</div>

<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mt-8 !bg-emerald-50 !border-emerald-100 mb-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-8 !bg-emerald-50 !border-emerald-100 mb-8']); ?>
    <p class="text-emerald-800 text-sm text-center">
        <strong>Efficiency Ratio:</strong> Your business is currently retaining 
        <span class="font-black text-lg text-emerald-900"><?php echo e(number_format($summary['profit'] / ($summary['revenue'] ?: 1) * 100, 1)); ?>%</span> 
        of every Rupee generated after all procurement and operational expenses.
    </p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Income vs Expense Trend']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Income vs Expense Trend']); ?>
    <canvas id="trendChart" class="w-full h-80"></canvas>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const weeklyData = <?php echo json_encode($weeklyData, 15, 512) ?>;
    
    const labels = weeklyData.map(d => d.week).reverse();
    const income = weeklyData.map(d => d.revenue).reverse();
    const expense = weeklyData.map(d => d.expenses + d.purchase).reverse();

    const ctxTrend = document.getElementById('trendChart').getContext('2d');

    const incGrad = ctxTrend.createLinearGradient(0, 0, 0, 400);
    incGrad.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    incGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');

    const expGrad = ctxTrend.createLinearGradient(0, 0, 0, 400);
    expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.4)');
    expGrad.addColorStop(1, 'rgba(244, 63, 94, 0)');

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Income (Revenue)',
                    data: income,
                    borderColor: 'rgba(16, 185, 129, 1)', // Emerald
                    backgroundColor: incGrad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgba(16, 185, 129, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Outflow (Purchases + Expenses)',
                    data: expense,
                    borderColor: 'rgba(244, 63, 94, 1)', // Rose
                    backgroundColor: expGrad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgba(244, 63, 94, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 20, font: { family: "'Outfit', sans-serif" } }
                },
                tooltip: {
                    backgroundColor: 'rgba(24, 24, 27, 0.9)',
                    titleFont: { family: "'Cabinet Grotesk', sans-serif", size: 14 },
                    bodyFont: { family: "'Outfit', sans-serif", size: 13 },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                    usePointStyle: true
                }
            },
            scales: {
                x: { 
                    grid: { display: false }, 
                    border: { display: false },
                    ticks: { font: { family: "'Outfit', sans-serif" } }
                },
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(161, 161, 170, 0.1)', borderDash: [5, 5] },
                    border: { display: false },
                    ticks: { font: { family: "'Outfit', sans-serif" } }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/profit/expense-vs-income.blade.php ENDPATH**/ ?>
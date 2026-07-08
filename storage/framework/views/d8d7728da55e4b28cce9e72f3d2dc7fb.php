<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['totalAmountId' => 'display-total', 'totalAmountRawId' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['totalAmountId' => 'display-total', 'totalAmountRawId' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="emiGenerator()" x-modelable="localPaymentMode" x-model="paymentMode" x-show="localPaymentMode === 'Pay later(EMI)'" style="display: none;" class="col-span-1 md:col-span-2 space-y-4 mb-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
    <div class="flex items-center gap-2 mb-2">
        <span class="material-symbols-rounded text-indigo-600 dark:text-indigo-400">calendar_month</span>
        <h3 class="text-sm font-bold text-zinc-800 dark:text-white">EMI Schedule Configuration</h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">No. of Months</label>
            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'number','xModel.number' => 'months','min' => '1','max' => '60','@input' => 'generateSchedule','class' => 'font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','x-model.number' => 'months','min' => '1','max' => '60','@input' => 'generateSchedule','class' => 'font-bold']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $attributes = $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $component = $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Start Date</label>
            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'date','xModel' => 'startDate','@change' => 'generateSchedule','class' => 'font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','x-model' => 'startDate','@change' => 'generateSchedule','class' => 'font-bold']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $attributes = $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $component = $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
        </div>
        <div class="flex items-end">
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','variant' => 'outline','@click' => 'generateSchedule','icon' => 'refresh','class' => 'w-full justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'outline','@click' => 'generateSchedule','icon' => 'refresh','class' => 'w-full justify-center']); ?>
                Regenerate
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
        </div>
    </div>

    <div x-show="schedules.length > 0" class="mt-4 border rounded-xl border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50">
                    <th class="px-4 py-2 text-xs font-bold text-zinc-500 uppercase tracking-wider">Inst. #</th>
                    <th class="px-4 py-2 text-xs font-bold text-zinc-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-4 py-2 text-xs font-bold text-zinc-500 uppercase tracking-wider">Amount (₹)</th>
                    <th class="px-4 py-2 text-xs font-bold text-zinc-500 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(emi, index) in schedules" :key="index">
                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                        <td class="px-4 py-2 text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="index + 1"></td>
                        <td class="px-4 py-2">
                            <input type="date" :name="`emis[${index}][due_date]`" x-model="emi.date" required class="w-full px-2 py-1 text-sm rounded border border-zinc-200 dark:border-zinc-700 bg-transparent" />
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" :name="`emis[${index}][amount]`" x-model.number="emi.amount" step="0.01" required @input="recalculateTotal" class="w-full px-2 py-1 text-sm rounded border border-zinc-200 dark:border-zinc-700 bg-transparent font-mono font-bold" />
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button type="button" @click="removeRow(index)" class="text-rose-500 hover:text-rose-700">
                                <span class="material-symbols-rounded text-sm">close</span>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr class="bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-700">
                    <td colspan="2" class="px-4 py-2 text-right text-xs font-bold text-zinc-500 uppercase">Total EMI Sum:</td>
                    <td colspan="2" class="px-4 py-2 font-mono font-bold text-zinc-900 dark:text-white">
                        ₹<span x-text="totalEmiAmount.toFixed(2)"></span>
                        <span x-show="isTotalMismatch()" class="text-rose-500 text-xs ml-2 block">Matches required: ₹<span x-text="getRequiredTotal().toFixed(2)"></span></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('emiGenerator', () => ({
        months: 3,
        startDate: new Date().toISOString().split('T')[0],
        schedules: (<?php echo json_encode(old('emis', []), 512) ?> || []).map(item => ({ date: item.due_date || item.date || '', amount: parseFloat(item.amount) || 0 })),
        totalEmiAmount: 0,
        localPaymentMode: '',

        getRequiredTotal() {
            let totalStr = '0';
            <?php if($totalAmountRawId): ?>
                totalStr = document.getElementById('<?php echo e($totalAmountRawId); ?>')?.value || '0';
            <?php else: ?>
                totalStr = document.getElementById('<?php echo e($totalAmountId); ?>')?.textContent || '0';
                totalStr = totalStr.replace(/[^0-9.]/g, '');
            <?php endif; ?>
            return parseFloat(totalStr) || 0;
        },

        generateSchedule() {
            const requiredTotal = this.getRequiredTotal();
            if (requiredTotal <= 0 || this.months <= 0) return;

            const baseAmount = Math.floor((requiredTotal / this.months) * 100) / 100;
            let remainder = requiredTotal - (baseAmount * this.months);
            
            this.schedules = [];
            let currentDt = new Date(this.startDate);

            for(let i=0; i < this.months; i++) {
                let amount = baseAmount;
                if (i === this.months - 1) {
                    amount += remainder;
                }
                
                let isoDate = currentDt.toISOString().split('T')[0];
                this.schedules.push({
                    date: isoDate,
                    amount: parseFloat(amount.toFixed(2))
                });
                currentDt.setMonth(currentDt.getMonth() + 1);
            }
            this.recalculateTotal();
        },

        removeRow(index) {
            this.schedules.splice(index, 1);
            this.recalculateTotal();
        },

        recalculateTotal() {
            this.totalEmiAmount = this.schedules.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },

        isTotalMismatch() {
            return Math.abs(this.totalEmiAmount - this.getRequiredTotal()) > 0.05;
        },

        init() {
            <?php if($totalAmountRawId): ?>
                const targetNode = document.getElementById('<?php echo e($totalAmountRawId); ?>');
            <?php else: ?>
                const targetNode = document.getElementById('<?php echo e($totalAmountId); ?>');
            <?php endif; ?>
            
            if (targetNode) {
                const observer = new MutationObserver(() => {
                    if (this.localPaymentMode === 'Pay later(EMI)') {
                        this.generateSchedule(); 
                    }
                });
                observer.observe(targetNode, { childList: true, characterData: true, subtree: true, attributes: true });
            }

            // Watch parent's paymentMode via localPaymentMode to generate schedule when selected
            this.$watch('localPaymentMode', (value) => {
                if (value === 'Pay later(EMI)') {
                    setTimeout(() => {
                        this.generateSchedule();
                    }, 50);
                }
            });

            // If initially Pay later(EMI) (e.g. redirect back on validation error)
            setTimeout(() => {
                this.recalculateTotal();
                if (this.localPaymentMode === 'Pay later(EMI)' && this.schedules.length === 0) {
                    this.generateSchedule();
                }
            }, 100);
        }
    }));
});
</script>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/components/emi-schedule-generator.blade.php ENDPATH**/ ?>
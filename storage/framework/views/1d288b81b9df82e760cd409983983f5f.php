
<?php $__env->startSection('title', 'Purchase Entry'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Purchase Entry & Refills','subtitle' => 'Record incoming inventory supply and map it to specific batches & locations']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Purchase Entry & Refills','subtitle' => 'Record incoming inventory supply and map it to specific batches & locations']); ?>
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('purchases.invoices')).'','variant' => 'secondary','icon' => 'receipt_long']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('purchases.invoices')).'','variant' => 'secondary','icon' => 'receipt_long']); ?>
            Invoice Archive
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
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('purchases.export')).'','variant' => 'secondary','icon' => 'download']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('purchases.export')).'','variant' => 'secondary','icon' => 'download']); ?>
            Export
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

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create purchases')): ?>
    
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'transition-all duration-300','xData' => '{ showForm: false }','xBind:class' => 'showForm ? \'ring-4 ring-emerald-50 dark:ring-emerald-900/30 border-emerald-100 dark:border-emerald-800\' : \'hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'transition-all duration-300','x-data' => '{ showForm: false }','x-bind:class' => 'showForm ? \'ring-4 ring-emerald-50 dark:ring-emerald-900/30 border-emerald-100 dark:border-emerald-800\' : \'hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]\'']); ?>
        <div class="flex justify-between items-center cursor-pointer" @click="showForm = !showForm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/20">
                    <span class="material-symbols-rounded text-[22px]">add_circle</span>
                </div>
                <div>
                    <h2 class="text-[1.1rem] font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight">Record Purchase</h2>
                    <p class="text-[0.75rem] font-semibold text-zinc-400 dark:text-zinc-500 mt-0.5 tracking-wide uppercase">Click to expand and fill details</p>
                </div>
            </div>
            <button type="button" class="flex items-center justify-center h-10 px-4 gap-2 rounded-xl text-sm transition-all duration-300 font-bold" :class="showForm ? 'bg-zinc-800 dark:bg-zinc-100 text-white dark:text-zinc-900' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700'">
                <span class="material-symbols-rounded" x-text="showForm ? 'expand_less' : 'add'"></span>
                <span x-text="showForm ? 'Close Panel' : 'New Entry'"></span>
            </button>
        </div>

        <?php if($errors->any()): ?>
            <div class="mt-4 mb-4 mx-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative">
                <ul class="list-disc pl-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="text-sm font-medium"><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="pt-8 mt-6 border-t border-zinc-100 dark:border-zinc-800">
            <form action="<?php echo e(route('purchases.store')); ?>" method="POST" id="purchase-form" x-data="{ paymentMode: 'Cash' }">
                <?php echo csrf_field(); ?>
                
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">1. Vendor / Partner <span class="text-rose-500">*</span></label>
                        <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'vendor_name','required' => true,'onchange' => 'updateVendorOutstanding()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'vendor_name','required' => true,'onchange' => 'updateVendorOutstanding()']); ?>
                            <option value="">Select supply partner...</option>
                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendor->firm_name); ?>" data-outstanding="<?php echo e($vendor->outstanding_balance); ?>" <?php echo e(old('vendor_name') === $vendor->firm_name ? 'selected' : ''); ?>>
                                    <?php echo e($vendor->firm_name); ?> <?php if($vendor->outstanding_balance > 0): ?> (Outstanding: ₹<?php echo e(number_format($vendor->outstanding_balance, 0)); ?>) <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $attributes = $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $component = $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
                        
                        <div id="vendor-outstanding-info" class="mt-2 text-xs font-bold text-amber-600 dark:text-amber-400 hidden items-center gap-1.5 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1.5 rounded-lg">
                            <span class="material-symbols-rounded text-[16px]">account_balance_wallet</span>
                            <span>Current Dues:</span>
                            <span id="vendor-outstanding-amount" class="font-mono text-amber-700 dark:text-amber-300 font-extrabold">₹0.00</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">2. Invoice Number / Bill ID <span class="text-xs text-zinc-400 font-normal">(Auto)</span></label>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'text','name' => 'invoice_no','value' => ''.e(old('invoice_no', $autoInvoiceNo)).'','readonly' => true,'class' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 cursor-not-allowed font-mono']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'invoice_no','value' => ''.e(old('invoice_no', $autoInvoiceNo)).'','readonly' => true,'class' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 cursor-not-allowed font-mono']); ?>
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
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">3. Billing Date <span class="text-rose-500">*</span></label>
                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'date','name' => 'date','required' => true,'value' => ''.e(old('date', date('Y-m-d'))).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date','required' => true,'value' => ''.e(old('date', date('Y-m-d'))).'']); ?>
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
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">4. Payment Mode <span class="text-rose-500">*</span></label>
                        <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'payment_mode','required' => true,'xModel' => 'paymentMode']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'payment_mode','required' => true,'x-model' => 'paymentMode']); ?>
                            <option value="Cash" <?php echo e(old('payment_mode', 'Cash') === 'Cash' ? 'selected' : ''); ?>>Cash</option>
                            <option value="Pay later(EMI)" <?php echo e(old('payment_mode') === 'Pay later(EMI)' ? 'selected' : ''); ?>>Pay later(EMI)</option>
                            <option value="UPI" <?php echo e(old('payment_mode') === 'UPI' ? 'selected' : ''); ?>>UPI</option>
                            <option value="NEFT" <?php echo e(old('payment_mode') === 'NEFT' ? 'selected' : ''); ?>>NEFT</option>
                            <option value="Cheque(Bank Transfer)" <?php echo e(old('payment_mode') === 'Cheque(Bank Transfer)' ? 'selected' : ''); ?>>Cheque(Bank Transfer)</option>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $attributes = $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $component = $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
                    </div>
                </div>

                <?php if (isset($component)) { $__componentOriginal3314d2403fda3bd00c9c7e42f1561019 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3314d2403fda3bd00c9c7e42f1561019 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.emi-schedule-generator','data' => ['totalAmountId' => 'display-total']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('emi-schedule-generator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['totalAmountId' => 'display-total']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3314d2403fda3bd00c9c7e42f1561019)): ?>
<?php $attributes = $__attributesOriginal3314d2403fda3bd00c9c7e42f1561019; ?>
<?php unset($__attributesOriginal3314d2403fda3bd00c9c7e42f1561019); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3314d2403fda3bd00c9c7e42f1561019)): ?>
<?php $component = $__componentOriginal3314d2403fda3bd00c9c7e42f1561019; ?>
<?php unset($__componentOriginal3314d2403fda3bd00c9c7e42f1561019); ?>
<?php endif; ?>

                
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                            <span class="material-symbols-rounded text-zinc-500">list_alt</span>
                            <span>Procured Products</span>
                        </div>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','variant' => 'secondary','onclick' => 'addRow()','size' => 'sm','icon' => 'add']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','onclick' => 'addRow()','size' => 'sm','icon' => 'add']); ?>
                            Add Item Row
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

                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm" id="items-table">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-3 w-[30%]">Product / Item <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-[15%]">Qty <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-[15%]">Unit</th>
                                    <th class="p-3 w-[15%]">Rate (₹) <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-[15%] text-right">Total Amount</th>
                                    <th class="p-3 w-[10%]"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr class="item-row hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                                    <td class="p-3">
                                        <input list="items-list" name="items[0][name]" required onchange="updateUnit(this)" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 item-selector focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" placeholder="Select or type product...">
                                        <datalist id="items-list">
                                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($item->name); ?>" data-unit="<?php echo e($item->base_unit); ?>"></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </datalist>
                                    </td>
                                    <td class="p-3">
                                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'number','name' => 'items[0][qty]','step' => '0.01','required' => true,'placeholder' => '0.00','class' => 'row-qty','oninput' => 'recalculate()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'items[0][qty]','step' => '0.01','required' => true,'placeholder' => '0.00','class' => 'row-qty','oninput' => 'recalculate()']); ?>
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
                                    </td>
                                    <td class="p-3">
                                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'text','name' => 'items[0][unit]','value' => 'kg','class' => 'row-unit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'items[0][unit]','value' => 'kg','class' => 'row-unit']); ?>
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
                                    </td>
                                    <td class="p-3">
                                        <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'number','name' => 'items[0][rate]','step' => '0.01','required' => true,'placeholder' => '0.00','class' => 'row-rate','oninput' => 'recalculate()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'items[0][rate]','step' => '0.01','required' => true,'placeholder' => '0.00','class' => 'row-rate','oninput' => 'recalculate()']); ?>
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
                                    </td>
                                    <td class="p-3 text-right font-bold text-zinc-800 dark:text-zinc-200 text-base">
                                        <span class="row-total">₹0.00</span>
                                    </td>
                                    <td class="p-3 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                    <div class="bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5">
                        <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-4">Tax Configuration</label>
                        <div class="flex items-center gap-4">
                            <div class="w-24">
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">GST %</label>
                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'number','name' => 'gst_percentage','id' => 'gst-percentage','value' => '18','step' => '0.1','class' => 'font-bold text-center','oninput' => 'recalculate()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'gst_percentage','id' => 'gst-percentage','value' => '18','step' => '0.1','class' => 'font-bold text-center','oninput' => 'recalculate()']); ?>
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
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">Computed GST Value</label>
                                <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'text','name' => 'display_tax','id' => 'display-tax','readonly' => true,'value' => '₹0.00','class' => 'bg-zinc-100 dark:bg-zinc-800 font-mono','tabindex' => '-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','name' => 'display_tax','id' => 'display-tax','readonly' => true,'value' => '₹0.00','class' => 'bg-zinc-100 dark:bg-zinc-800 font-mono','tabindex' => '-1']); ?>
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
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-6 shadow-xl shadow-emerald-500/20 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div>
                            <span class="text-emerald-100 text-xs font-bold uppercase tracking-wider block mb-1">Final Grand Net Total</span>
                            <span id="display-total" class="text-3xl font-black font-mono tracking-tight">₹0.00</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-white text-emerald-700 hover:bg-emerald-50 px-6 py-3 rounded-lg font-bold flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-md">
                            <span class="material-symbols-rounded">check_circle</span>
                            Save Purchase
                        </button>
                    </div>
                </div>
            </form>
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
        <div class="flex flex-col sm:flex-row gap-4 mb-6 justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Vendor Bird Supply</h2>
                <p class="text-xs text-zinc-500 mt-1">Day-load birds supplied by vendors</p>
            </div>
            <div class="flex gap-3 text-xs font-bold">
                <span class="px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                    <span class="material-symbols-rounded text-[14px] align-text-bottom">inventory_2</span>
                    <?php echo e(number_format($vendorDayLoadTotalBoxes)); ?> Boxes
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="material-symbols-rounded text-[14px] align-text-bottom">scale</span>
                    <?php echo e(number_format($vendorDayLoadTotalBird, 1)); ?> kg Bird
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                    <span class="material-symbols-rounded text-[14px] align-text-bottom">agriculture</span>
                    <?php echo e(number_format($vendorDayLoadTotalFarm, 1)); ?> kg Farm
                </span>
            </div>
        </div>

        <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => ['Date', 'Vendor', ['label' => 'Boxes', 'align' => 'right'], ['label' => 'Bird Weight', 'align' => 'right'], ['label' => 'Farm Weight', 'align' => 'right'], ['label' => 'Loss', 'align' => 'right']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Date', 'Vendor', ['label' => 'Boxes', 'align' => 'right'], ['label' => 'Bird Weight', 'align' => 'right'], ['label' => 'Farm Weight', 'align' => 'right'], ['label' => 'Loss', 'align' => 'right']])]); ?>
            <?php $__empty_1 = true; $__currentLoopData = $vendorDayLoads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50">
                    <td class="px-4 py-3 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        <?php echo e($entry->batch->billing_date->format('d M Y')); ?>

                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => ''.e($entry->vendor->firm_name ?? '-').'','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($entry->vendor->firm_name ?? '-').'','size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm"><?php echo e($entry->vendor->firm_name ?? '-'); ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm"><?php echo e($entry->no_of_boxes); ?></td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm"><?php echo e(number_format($entry->bird_weight, 1)); ?> kg</td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm"><?php echo e(number_format($entry->farm_weight ?? 0, 1)); ?> kg</td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm">
                        <?php if(($entry->loss_weight ?? 0) > 0): ?>
                            <span class="text-rose-600 dark:text-rose-400"><?php echo e(number_format($entry->loss_weight, 1)); ?> kg</span>
                        <?php else: ?>
                            <span class="text-emerald-600 dark:text-emerald-400">0 kg</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center py-8 text-zinc-500">No vendor day-load entries found.</td></tr>
            <?php endif; ?>
            <?php if($vendorDayLoads->hasPages()): ?>
                 <?php $__env->slot('pagination', null, []); ?> 
                    <?php echo e($vendorDayLoads->links()); ?>

                 <?php $__env->endSlot(); ?>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
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
        <div class="flex flex-col sm:flex-row gap-4 mb-6 justify-between items-center">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Recent Purchase Logs</h2>
            <form method="GET" class="flex w-full sm:w-80">
                <?php if (isset($component)) { $__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search','data' => ['name' => 'search','value' => ''.e($search).'','placeholder' => 'Search vendor or product name...','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','value' => ''.e($search).'','placeholder' => 'Search vendor or product name...','class' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6)): ?>
<?php $attributes = $__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6; ?>
<?php unset($__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6)): ?>
<?php $component = $__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6; ?>
<?php unset($__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6); ?>
<?php endif; ?>
            </form>
        </div>

        <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('header', null, []); ?> 
                <tr>
                    <th class="w-[15%]">Date</th>
                    <th class="w-[25%]">Vendor & Invoice ID</th>
                    <th class="w-[30%]">Refilled Products</th>
                    <th class="w-[15%] text-right">Net Amount</th>
                    <th class="w-[10%] text-center">Payment Mode</th>
                    <th class="w-[5%] text-center">Action</th>
                </tr>
             <?php $__env->endSlot(); ?>
            <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 font-medium">
                        <?php echo e($p->date->format('d M Y')); ?>

                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => ''.e($p->vendor_name).'','class' => 'bg-indigo-100 text-indigo-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($p->vendor_name).'','class' => 'bg-indigo-100 text-indigo-700']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                            <div>
                                <span class="block font-bold text-zinc-900 dark:text-zinc-100"><?php echo e($p->vendor_name); ?></span>
                                <span class="block text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Bill ID: <?php echo e($p->invoice_no ?: 'N/A'); ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1.5">
                            <?php $__currentLoopData = $p->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[11px] font-medium text-zinc-700 dark:text-zinc-300">
                                    <?php echo e($item->item_name); ?> <b class="ml-1">(<?php echo e(number_format($item->quantity)); ?> <?php echo e($item->unit); ?>)</b>
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-black text-zinc-900 dark:text-zinc-100 text-base">
                        <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $p->total_amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->total_amount)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php
                            $paymentColor = match(strtolower($p->payment_mode)) {
                                'cash' => 'emerald',
                                'upi' => 'indigo',
                                'neft' => 'sky',
                                'credit' => 'rose',
                                default => 'slate',
                            };
                        ?>
                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => $paymentColor]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paymentColor)]); ?><?php echo e($p->payment_mode); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','size' => 'sm','href' => ''.e(route('purchases.show', $p->id)).'','icon' => 'visibility','class' => 'text-sky-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','size' => 'sm','href' => ''.e(route('purchases.show', $p->id)).'','icon' => 'visibility','class' => 'text-sky-600']); ?>
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
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit purchases')): ?>
                                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'ghost','size' => 'sm','href' => ''.e(route('purchases.edit', $p->id)).'','icon' => 'edit','class' => 'text-amber-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'ghost','size' => 'sm','href' => ''.e(route('purchases.edit', $p->id)).'','icon' => 'edit','class' => 'text-amber-600']); ?>
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
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-12">
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'receipt','title' => 'No purchases matched','description' => 'Adjust your filter query or record a new purchase above']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'receipt','title' => 'No purchases matched','description' => 'Adjust your filter query or record a new purchase above']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
        
        <?php if($purchases->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($purchases->withQueryString()->links()); ?>

            </div>
        <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>

let rowCount = 1;

const ITEM_OPTIONS = `<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($item->name); ?>" data-unit="<?php echo e($item->base_unit); ?>"></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>`;

function addRow() {
    const body = document.getElementById('items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors border-t border-zinc-100 dark:border-zinc-800';
    newRow.innerHTML = `
        <td class="p-3">
            <input list="items-list" name="items[${rowCount}][name]" required onchange="updateUnit(this)" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 item-selector focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" placeholder="Select or type product...">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 row-qty focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" oninput="recalculate()">
        </td>
        <td class="p-3">
            <input type="text" name="items[${rowCount}][unit]" value="kg" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 row-unit focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 row-rate focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" oninput="recalculate()">
        </td>
        <td class="p-3 text-right font-bold text-zinc-800 dark:text-zinc-200 text-base">
            <span class="row-total">₹0.00</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors" title="Remove row">
                <span class="material-symbols-rounded text-[18px]">delete</span>
            </button>
        </td>
    `;
    body.appendChild(newRow);
    rowCount++;
}

function updateUnit(input) {
    const list = document.getElementById('items-list');
    if (!list) return;
    const options = list.options;
    for (let i = 0; i < options.length; i++) {
        if (options[i].value === input.value) {
            const unit = options[i].getAttribute('data-unit');
            const row = input.closest('tr');
            if (unit && row) {
                row.querySelector('.row-unit').value = unit;
            }
            return;
        }
    }
}

function recalculate() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;
        
        row.querySelector('.row-total').textContent = '₹' + total.toFixed(2);
        subtotal += total;
    });
    
    const gstInput = document.getElementById('gst_percentage') || document.getElementById('gst-percentage');
    const gstPercentage = parseFloat(gstInput ? gstInput.value : 0) || 0;
    const gstAmt = subtotal * gstPercentage / 100;
    const finalTotal = subtotal + gstAmt;
    
    const displayTax = document.getElementById('display_tax') || document.getElementById('display-tax');
    if (displayTax) {
        displayTax.value = '₹' + gstAmt.toFixed(2);
    }
    
    const displayTotal = document.getElementById('display-total');
    if (displayTotal) {
        displayTotal.textContent = '₹' + finalTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }
}


function updateVendorOutstanding() {
    const vendorSelect = document.querySelector('select[name="vendor_name"]');
    const outstandingInfo = document.getElementById('vendor-outstanding-info');
    const outstandingAmount = document.getElementById('vendor-outstanding-amount');
    
    if (!vendorSelect || !outstandingInfo || !outstandingAmount) return;
    
    const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        outstandingInfo.classList.add('hidden');
        outstandingInfo.classList.remove('flex');
        return;
    }
    
    const outstanding = parseFloat(selectedOption.getAttribute('data-outstanding')) || 0;
    
    if (outstanding > 0) {
        outstandingAmount.textContent = '₹' + outstanding.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        outstandingInfo.classList.remove('hidden');
        outstandingInfo.classList.add('flex');
    } else {
        outstandingInfo.classList.add('hidden');
        outstandingInfo.classList.remove('flex');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const selector = document.querySelector('.item-selector');
    if (selector && selector.value) updateUnit(selector);
    
    updateVendorOutstanding();
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\purchases\index.blade.php ENDPATH**/ ?>
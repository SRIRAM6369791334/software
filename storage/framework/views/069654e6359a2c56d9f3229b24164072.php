<?php $__env->startSection('title', 'Daily Sale Invoice #' . $bill->id); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-10 border border-zinc-200 shadow-lg rounded-2xl my-6 relative overflow-hidden" id="invoice-print">
    
    <div class="absolute top-0 left-0 w-full h-2 bg-emerald-600"></div>

    <div class="flex justify-between items-start border-b border-zinc-200 pb-8 mb-8">
        <div>
            <h1 class="text-4xl font-black text-emerald-600 tracking-tighter italic">Poultry <span class="text-zinc-950 not-italic tracking-normal font-bold"></span></h1>
            <p class="text-[10px] text-zinc-400 mt-1.5 uppercase tracking-[0.2em] font-black bg-emerald-50 px-3 py-1 rounded-full inline-block">Poultry Management Solutions</p>
            <div class="mt-6 text-sm text-zinc-500 space-y-1">
                <p class="font-bold text-zinc-950">Poultry Farm Unit #1</p>
                <p>Tamil Nadu, India</p>
                <p> +91 98765 43210</p>
            </div>
        </div>
        <div class="text-right">
            <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl inline-block mb-4">
                <h2 class="text-xl font-black uppercase tracking-tight">Tax Invoice</h2>
            </div>
            <p class="text-xs text-zinc-400 font-bold uppercase tracking-widest">Invoice Number</p>
            <p class="text-lg font-black text-zinc-950 font-mono"><?php echo e($bill->invoice_number); ?></p>
            <div class="mt-4">
                <p class="text-xs text-zinc-400 font-bold uppercase tracking-widest">Date of Issue</p>
                <p class="text-sm font-bold text-zinc-950"><?php echo e($bill->date->format('d F, Y')); ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-16 mb-12">
        <div>
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 border-b border-emerald-100 pb-1 inline-block">Bill To Customer</p>
            <h3 class="text-2xl font-black text-zinc-950"><?php echo e($bill->customer->name ?? 'N/A'); ?></h3>
            <div class="mt-3 text-sm text-zinc-600 leading-relaxed space-y-1">
                <p><?php echo e($bill->customer->address ?? 'No address provided'); ?></p>
                <p class="font-bold text-zinc-950"> <?php echo e($bill->customer->phone ?? 'N/A'); ?></p>
                <?php if($bill->customer->gst_number): ?>
                    <div class="mt-4 pt-4 border-t border-zinc-100">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Customer GSTIN</p>
                        <p class="text-sm font-mono font-bold text-zinc-700"><?php echo e($bill->customer->gst_number); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="bg-emerald-50 rounded-2xl p-6 border border-zinc-200">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-4">Payment Summary</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-zinc-500">Payment Status</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest"><?php echo e($bill->status); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-zinc-500">Billing Type</span>
                    <span class="text-xs font-bold text-zinc-950">Daily Retail Sale</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-zinc-200">
                    <span class="text-xs font-bold text-zinc-950">Currency</span>
                    <span class="text-xs font-bold text-zinc-950 italic">Indian Rupee (INR)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-12 overflow-hidden rounded-2xl border border-zinc-200 shadow-sm">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-zinc-200 text-[10px] font-black text-emerald-800 uppercase tracking-widest">
                    <th class="px-8 py-4">Item Description</th>
                    <th class="px-8 py-4 text-center">Quantity</th>
                    <th class="px-8 py-4 text-right">Unit Price</th>
                    <th class="px-8 py-4 text-right">Taxable Amt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="text-zinc-950 hover:bg-emerald-50 transition-colors">
                    <td class="px-8 py-6">
                        <p class="font-black text-zinc-950"><?php echo e($item->item_name); ?></p>
                        <p class="text-[10px] text-zinc-400 mt-0.5 uppercase tracking-tighter font-bold">Standard Poultry Product</p>
                    </td>
                    <td class="px-8 py-6 text-center font-mono font-bold text-zinc-600"><?php echo e(number_format($item->quantity_kg, 2)); ?> <?php echo e($item->unit); ?></td>
                    <td class="px-8 py-6 text-right font-mono text-zinc-600">Rs <?php echo e(number_format($item->rate_per_kg, 2)); ?></td>
                    <td class="px-8 py-6 text-right font-mono font-black text-zinc-950">Rs <?php echo e(number_format($item->quantity_kg * $item->rate_per_kg, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mb-16">
        <div class="w-72 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-zinc-200 rounded-3xl p-8 text-zinc-800 shadow-lg relative overflow-hidden">
            
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/20 rounded-full"></div>
            
            <div class="space-y-4 relative z-10">
                <div class="flex justify-between items-center text-xs opacity-60 font-bold uppercase tracking-widest">
                    <span>Subtotal</span>
                    <span>Rs <?php echo e(number_format($bill->amount, 2)); ?></span>
                </div>
                <div class="flex justify-between items-center text-xs opacity-60 font-bold uppercase tracking-widest pb-4 border-b border-zinc-200">
                    <span>GST (<?php echo e($bill->gst_percentage); ?>%)</span>
                    <span>Rs <?php echo e(number_format($bill->gst_amount, 2)); ?></span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs font-black uppercase tracking-[0.2em] text-emerald-600">Total Net</span>
                    <span class="text-3xl font-black font-mono">Rs <?php echo e(number_format($bill->net_amount, 2)); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-zinc-200 pt-10 text-center">
        <div class="flex justify-center gap-12 mb-8 text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em]">
            <span>No Signature Required</span>
            <span class="text-emerald-500">Computer Generated</span>
            <span>Auth Verified</span>
        </div>
        <p class="text-sm text-zinc-950 font-black mb-1">Thank you for choosing Poultry Management!</p>
        <p class="text-xs text-zinc-400 font-medium">Please settle the payment according to the agreed credit terms.</p>
        
        <div class="mt-10 flex justify-center gap-4 no-print">
            <button onclick="window.print()" class="px-8 py-3 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border-2 border-emerald-600 text-emerald-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-50 transition-all shadow-md shadow-emerald-600/5 active:scale-95"> Print Invoice</button>
            <a href="<?php echo e(route('billing.daily.pdf', $bill)); ?>" class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95 flex items-center gap-2"> Download PDF</a>
            <button onclick="window.close()" class="px-8 py-3 border-2 border-zinc-200 text-zinc-400 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-50 transition-all active:scale-95">Close Window</button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, aside, header { display: none !important; }
    body { background-color: white !important; padding: 0 !important; margin: 0 !important; }
    #invoice-print { 
        margin: 0 !important; 
        padding: 40px !important; 
        border: none !important; 
        box-shadow: none !important; 
        width: 100% !important; 
        max-width: none !important; 
        border-radius: 0 !important;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/billing/daily/invoice.blade.php ENDPATH**/ ?>
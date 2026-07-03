
<?php $__env->startSection('title', 'Invoice #' . ($bill->invoice_number ?? $bill->id)); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 border border-zinc-200 shadow-lg rounded-xl my-4" id="invoice-print">
    <div class="flex justify-between items-start border-b pb-6 mb-6">
        <div>
            <h1 class="text-3xl font-black text-emerald-600 tracking-tighter italic">Poultry <span class="text-zinc-950 not-italic tracking-normal font-bold"></span></h1>
            <p class="text-xs text-zinc-400 mt-1 uppercase tracking-widest font-semibold text-center bg-emerald-50 py-1 rounded">Poultry Management Solutions</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-zinc-950">INVOICE</h2>
            <p class="text-sm text-zinc-500 font-mono mt-1">#<?php echo e($bill->invoice_number); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-12 mb-10">
        <div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Bill To</p>
            <h3 class="text-lg font-bold text-zinc-950"><?php echo e($bill->dealer?->firm_name ?? 'N/A'); ?></h3>
            <p class="text-sm text-zinc-600 mt-1 leading-relaxed"><?php echo e($bill->dealer?->location ?? 'No address provided'); ?></p>
            <p class="text-sm text-zinc-600 font-medium mt-1"> <?php echo e($bill->dealer?->phone ?? 'N/A'); ?></p>
            <?php if($bill->dealer?->gst_number): ?>
                <p class="text-xs text-zinc-400 mt-2">GSTIN: <span class="text-zinc-700 font-mono"><?php echo e($bill->dealer->gst_number); ?></span></p>
            <?php endif; ?>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Invoice Details</p>
            <div class="space-y-2">
                <p class="text-sm text-zinc-600">Date: <span class="font-semibold text-zinc-950"><?php echo e(date('d M Y')); ?></span></p>
                <p class="text-sm text-zinc-600">Period: <span class="font-semibold text-zinc-950 italic"><?php echo e($bill->period_start?->format('d M') ?? 'N/A'); ?> - <?php echo e($bill->period_end?->format('d M Y') ?? 'N/A'); ?></span></p>
                <p class="text-sm text-zinc-600">Status: <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase"><?php echo e($bill->status); ?></span></p>
            </div>
        </div>
    </div>

    <div class="mb-10 overflow-hidden rounded-xl border border-zinc-200">
        <div class="px-6 py-3 bg-emerald-50 border-b border-zinc-200 flex justify-between items-center">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Line Items</span>
            <button type="button" onclick="toggleVendors()" id="vendor-toggle-btn" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 uppercase tracking-wider">
                <span class="material-symbols-rounded text-sm">visibility</span>
                Show Vendors
            </button>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-emerald-50 text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-200">
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4 vendor-col hidden">Vendor</th>
                    <th class="px-6 py-4 text-right">Quantity</th>
                    <th class="px-6 py-4 text-right">Unit Price</th>
                    <th class="px-6 py-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                <?php $__empty_1 = true; $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-6 py-5">
                        <p class="text-sm font-bold text-zinc-950"><?php echo e($item->item_name); ?></p>
                    </td>
                    <td class="px-6 py-5 vendor-col hidden">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                            <?php echo e($item->vendor_name ?? '-'); ?>

                        </span>
                    </td>
                    <td class="px-6 py-5 text-right font-mono text-sm text-zinc-600"><?php echo e(number_format($item->quantity_kg, 2)); ?> kg</td>
                    <td class="px-6 py-5 text-right font-mono text-sm text-zinc-600">Rs <?php echo e(number_format($item->rate_per_kg, 2)); ?></td>
                    <td class="px-6 py-5 text-right font-mono font-bold text-zinc-950">Rs <?php echo e(number_format($item->quantity_kg * $item->rate_per_kg, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td class="px-6 py-5">
                        <p class="text-sm font-bold text-zinc-950"><?php echo e($bill->items_description ?: 'Livestock/Poultry Products'); ?></p>
                        <p class="text-xs text-zinc-400 mt-0.5">Weekly supply for cycle <?php echo e($bill->period_start?->format('M d') ?? 'N/A'); ?></p>
                    </td>
                    <td class="px-6 py-5 text-right font-mono text-sm text-zinc-600"><?php echo e(number_format($bill->quantity_kg, 2)); ?> kg</td>
                    <td class="px-6 py-5 text-right font-mono text-sm text-zinc-600">Rs <?php echo e(number_format($bill->amount / ($bill->quantity_kg ?: 1), 2)); ?></td>
                    <td class="px-6 py-5 text-right font-mono font-bold text-zinc-950">Rs <?php echo e(number_format($bill->amount, 2)); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mb-12">
        <div class="w-64 bg-gradient-to-r from-emerald-50/80 to-sky-50/80 rounded-2xl p-6 border border-zinc-200">
            <div class="flex justify-between items-center mb-3">
                <span class="text-xs text-zinc-500 font-medium">Subtotal</span>
                <span class="text-sm font-mono text-zinc-950 font-semibold">Rs <?php echo e(number_format($bill->amount, 2)); ?></span>
            </div>
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-zinc-200">
                <span class="text-xs text-zinc-500 font-medium">Tax (<?php echo e($bill->gst_percentage ?? 0); ?>%)</span>
                <span class="text-sm font-mono text-zinc-950 font-semibold">Rs <?php echo e(number_format($bill->gst_amount ?? 0, 2)); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-zinc-950 uppercase tracking-wider">Total Due</span>
                <span class="text-xl font-black text-emerald-600 font-mono">Rs <?php echo e(number_format($bill->net_amount ?? $bill->amount, 2)); ?></span>
            </div>
        </div>
    </div>

    <div class="border-t pt-8 text-center">
        <p class="text-sm text-zinc-950 font-bold mb-1">Thank you for your business!</p>
        <p class="text-xs text-zinc-400">Please settle the payment within 7 days of invoice generation.</p>
        <div class="mt-8 flex justify-center gap-4 no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-zinc-200 text-emerald-700 text-sm font-bold rounded-lg hover:bg-emerald-50 transition-all shadow-lg hover:shadow-zinc-200/60"> Print Invoice</button>
            <button onclick="window.close()" class="px-6 py-2 border border-zinc-200 text-zinc-600 text-sm font-bold rounded-lg hover:bg-emerald-50 transition-all">Close</button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, aside, header { display: none !important; }
    body { background-color: white !important; }
    #invoice-print { 
        margin: 0 !important; 
        padding: 0 !important; 
        border: none !important; 
        box-shadow: none !important; 
        width: 100% !important; 
        max-width: none !important; 
    }
}
</style>

<script>
let vendorsVisible = false;
function toggleVendors() {
    vendorsVisible = !vendorsVisible;
    document.querySelectorAll('.vendor-col').forEach(el => {
        el.classList.toggle('hidden', !vendorsVisible);
    });
    const btn = document.getElementById('vendor-toggle-btn');
    btn.innerHTML = vendorsVisible
        ? '<span class="material-symbols-rounded text-sm">visibility_off</span> Hide Vendors'
        : '<span class="material-symbols-rounded text-sm">visibility</span> Show Vendors';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/billing/invoice.blade.php ENDPATH**/ ?>
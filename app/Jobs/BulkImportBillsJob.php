<?php

namespace App\Jobs;

use App\Models\WeeklyBill;
use App\Models\WeeklyBillItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkImportBillsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $billsData) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Asynchronously processing bulk bills import dataset: " . count($this->billsData) . " records.");

        try {
            DB::transaction(function () {
                foreach ($this->billsData as $billRow) {
                    // Simulating validation & creation of a weekly bill record
                    $bill = WeeklyBill::create([
                        'customer_id' => $billRow['customer_id'],
                        'period_start' => $billRow['period_start'],
                        'period_end' => $billRow['period_end'],
                        'net_amount' => $billRow['net_amount'],
                        'gst_amount' => $billRow['gst_amount'],
                        'total_amount' => $billRow['total_amount'],
                    ]);

                    if (isset($billRow['items'])) {
                        foreach ($billRow['items'] as $itemRow) {
                            WeeklyBillItem::create([
                                'weekly_bill_id' => $bill->id,
                                'item_name' => $itemRow['item_name'],
                                'quantity' => $itemRow['quantity'],
                                'rate' => $itemRow['rate'],
                                'total_amount' => $itemRow['total_amount'],
                            ]);
                        }
                    }
                }
            });
            Log::info("Successfully imported bulk bill records in background.");
        } catch (\Exception $e) {
            Log::error("Failed to import bulk bill records in background: " . $e->getMessage());
        }
    }
}

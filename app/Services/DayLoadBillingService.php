<?php

namespace App\Services;

use App\Exceptions\BatchLockedException;
use App\Exceptions\BoxConservationException;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\DayLoadInvoice;
use App\Models\EntryAdjustmentLog;
use Illuminate\Support\Facades\DB;

class DayLoadBillingService
{
    public function __construct(
        private InvoiceNumberService $invoiceService,
    ) {}

    /**
     * Create a new day-load entry within a batch for the given billing_date.
     *
     * @throws BatchLockedException if the batch is Locked
     */
    public function createEntry(array $data): DayLoadEntry
    {
        return DB::transaction(function () use ($data) {
            $batch = DayLoadBatch::firstOrCreate(
                ['billing_date' => $data['billing_date']],
                ['status' => 'Open']
            );

            if ($batch->status === 'Locked') {
                throw new BatchLockedException(
                    "Cannot add entry: batch for {$batch->billing_date->format('Y-m-d')} is locked."
                );
            }

            $isLateEntry = $batch->status === 'Invoiced';

            $entry = DayLoadEntry::create([
                'batch_id'     => $batch->id,
                'vendor_id'    => $data['vendor_id'],
                'dealer_id'    => $data['dealer_id'],
                'paper_rate'   => $data['paper_rate'],
                'billing_rate' => $data['billing_rate'],
                'customer_rate'=> $data['customer_rate'],
                'no_of_boxes'  => $data['no_of_boxes'],
                'box_weight'   => $data['box_weight'],
                'empty_weight' => $data['empty_weight'],
                'farm_weight'  => $data['farm_weight'] ?? null,
                'remarks'      => $data['remarks'] ?? null,
            ]);

            $logReason = $isLateEntry
                ? 'Entry created (added after batch was invoiced — invoice version bumped to sync)'
                : 'Entry created';

            EntryAdjustmentLog::create([
                'entry_id'            => $entry->id,
                'action_type'         => 'Create',
                'old_values'          => null,
                'new_values'          => $entry->toArray(),
                'resulting_entry_id'  => null,
                'reason'              => $logReason,
                'adjusted_by'         => auth()->id(),
            ]);

            $this->refreshBatchTotals($batch);

            return $entry;
        });
    }

    /**
     * Recalculate all aggregate totals on a batch from its qualifying entries.
     *
     * Includes entries with status 'Active' or 'Adjusted'.
     * Excludes 'Cancelled' and 'Split' (no code path assigns 'Split',
     * but the enum value exists defensively).
     */
    public function refreshBatchTotals(DayLoadBatch $batch): void
    {
        $totals = $batch->entries()
            ->where('status', '!=', 'Cancelled')
            ->selectRaw('
                COALESCE(SUM(no_of_boxes), 0) as total_boxes,
                COALESCE(SUM(box_weight), 0) as total_box_weight,
                COALESCE(SUM(empty_weight), 0) as total_empty_weight,
                COALESCE(SUM(bird_weight), 0) as total_bird_weight,
                COALESCE(SUM(COALESCE(farm_weight, 0)), 0) as total_farm_weight,
                COALESCE(SUM(COALESCE(total_weight, bird_weight)), 0) as total_weight,
                COALESCE(SUM(COALESCE(loss_weight, 0)), 0) as total_loss_weight
            ')
            ->first();

        $totalFarmWeight = (float) $totals->total_farm_weight;
        $totalLossWeight = (float) $totals->total_loss_weight;
        $totalWeight = (float) $totals->total_weight;

        if ($totalLossWeight == 0 && $totalFarmWeight > (float) $totals->total_bird_weight) {
            $totalLossWeight = round($totalFarmWeight - (float) $totals->total_bird_weight, 2);
            $totalWeight = (float) $totals->total_bird_weight;
        }

        $entriesWithLoss = $batch->entries()->where('status', '!=', 'Cancelled')->get();
        $weightLossAmount = (float) $entriesWithLoss->sum(function ($e) {
            $rate = (float) ($e->billing_rate ?: ($e->vendor_rate ?: $e->paper_rate));
            return (float) ($e->loss_weight ?? 0) * $rate;
        });

        $batch->update([
            'total_boxes'        => $totals->total_boxes,
            'total_box_weight'   => $totals->total_box_weight,
            'total_empty_weight' => $totals->total_empty_weight,
            'total_bird_weight'  => $totals->total_bird_weight,
            'total_farm_weight'  => $totalFarmWeight,
            'total_weight'       => $totalWeight,
            'total_loss_weight'  => $totalLossWeight,
            'weight_loss_amount' => round($weightLossAmount, 2),
        ]);

        if ($batch->invoice) {
            $this->syncInvoice($batch);
        }
    }

    /**
     * Approve Weight Loss Expense for a Day-Load Batch and sync into Cash & Bank Ledger.
     */
    public function approveWeightLoss(DayLoadBatch $batch, int $userId): DayLoadBatch
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($batch, $userId) {
            $this->refreshBatchTotals($batch);

            $batch->update([
                'is_weight_loss_approved' => true,
                'weight_loss_approved_by' => $userId,
                'weight_loss_approved_at' => now(),
            ]);

            $lossAmount = (float) $batch->weight_loss_amount;
            if ($lossAmount > 0) {
                $billingDate = $batch->billing_date->format('Y-m-d');
                $desc = "Weight Loss Expense - ({$batch->total_loss_weight} kg)";

                \App\Models\Expense::updateOrCreate(
                    [
                        'date'     => $billingDate,
                        'category' => 'Weight Loss',
                    ],
                    [
                        'description'    => $desc,
                        'amount'         => $lossAmount,
                        'payment_method' => 'Cash',
                    ]
                );

                app(CashBankLedgerService::class)->recalculateForDate($batch->billing_date);
            }

            return $batch->fresh();
        });
    }

    /**
     * Split an entry: shrink the original and create a child entry for the split portion.
     *
     * The original entry's status becomes 'Adjusted'. The new child entry is 'Active'.
     * Box conservation is strictly enforced — the sum of remaining + split must equal
     * the original no_of_boxes exactly.
     *
     * @throws BoxConservationException
     */
    public function splitEntry(DayLoadEntry $original, array $splitData, string $reason): array
    {
        return DB::transaction(function () use ($original, $splitData, $reason) {
            $remainingBoxes = (int) $splitData['remaining_boxes'];
            $splitBoxes = (int) $splitData['split_boxes'];

            if ($remainingBoxes + $splitBoxes !== (int) $original->no_of_boxes) {
                throw new BoxConservationException(
                    "Box conservation violated: remaining ({$remainingBoxes}) + split ({$splitBoxes}) "
                    . "does not equal original ({$original->no_of_boxes})."
                );
            }

            // Snapshot old values BEFORE any writes
            $oldValues = $original->toArray();

            // Calculate proportional weight reductions
            $ratio = $splitBoxes / (float) $original->no_of_boxes;
            $originalBoxWeight = (float) $original->box_weight;
            $originalEmptyWeight = (float) $original->empty_weight;
            $splitBoxWeight = round($originalBoxWeight * $ratio, 2);
            $splitEmptyWeight = round($originalEmptyWeight * $ratio, 2);

            // Shrink original entry
            $original->update([
                'no_of_boxes'  => $remainingBoxes,
                'box_weight'   => round($originalBoxWeight - $splitBoxWeight, 2),
                'empty_weight' => round($originalEmptyWeight - $splitEmptyWeight, 2),
                'status'       => 'Adjusted',
                'version'      => $original->version + 1,
            ]);

            // Create child entry for the split portion
            $newEntry = DayLoadEntry::create([
                'batch_id'        => $original->batch_id,
                'vendor_id'       => $original->vendor_id,
                'dealer_id'       => $splitData['dealer_id'],
                'paper_rate'      => $splitData['paper_rate'] ?? $original->paper_rate,
                'billing_rate'    => $splitData['billing_rate'] ?? $original->billing_rate,
                'customer_rate'   => $splitData['customer_rate'] ?? $original->customer_rate,
                'no_of_boxes'     => $splitBoxes,
                'box_weight'      => $splitBoxWeight,
                'empty_weight'    => $splitEmptyWeight,
                'farm_weight'     => $splitData['farm_weight'] ?? null,
                'status'          => 'Active',
                'parent_entry_id' => $original->id,
                'version'         => 1,
                'remarks'         => $splitData['remarks'] ?? null,
            ]);

            EntryAdjustmentLog::create([
                'entry_id'           => $original->id,
                'action_type'        => 'Split',
                'old_values'         => $oldValues,
                'new_values'         => [
                    'original_after' => $original->fresh()->toArray(),
                    'new_entry'      => $newEntry->toArray(),
                ],
                'resulting_entry_id' => $newEntry->id,
                'reason'             => $reason,
                'adjusted_by'        => auth()->id(),
            ]);

            $batch = $original->batch;
            $this->refreshBatchTotals($batch);

            return [$original->fresh(), $newEntry];
        });
    }

    /**
     * Transfer bird weight from one entry to a target dealer/vendor.
     *
     * Shrinks the source entry proportionally and either adds to an existing
     * target entry (same vendor+dealer+batch) or creates a new one.
     *
     * @throws BatchLockedException if the batch is Locked
     * @throws \InvalidArgumentException if transfer_weight is invalid
     */
    public function transferWeight(DayLoadEntry $source, array $transferData, string $reason): array
    {
        return DB::transaction(function () use ($source, $transferData, $reason) {
            $batch = $source->batch;

            if ($batch->status === 'Locked') {
                throw new BatchLockedException(
                    "Cannot transfer weight: batch for {$batch->billing_date->format('Y-m-d')} is locked."
                );
            }

            $transferWeight = (float) $transferData['transfer_weight'];
            $targetDealerId = (int) $transferData['target_dealer_id'];
            $targetVendorId = (int) $transferData['target_vendor_id'];

            if ($transferWeight <= 0 || $transferWeight > (float) $source->bird_weight) {
                throw new \InvalidArgumentException(
                    "Transfer weight ({$transferWeight} kg) must be between 0.01 and {$source->bird_weight} kg."
                );
            }

            $oldValues = $source->toArray();

            // Calculate ratio based on transferred bird_weight to total bird_weight
            $ratio = $transferWeight / (float) $source->bird_weight;
            if ($ratio > 1.0) {
                $ratio = 1.0;
            }

            $sourceBoxWeight = (float) $source->box_weight;
            $sourceEmptyWeight = (float) $source->empty_weight;

            $isFullTransfer = abs($transferWeight - (float) $source->bird_weight) < 0.01;

            if ($isFullTransfer) {
                $transferBoxes = (int) $source->no_of_boxes;
                $transferBoxWeight = $sourceBoxWeight;
                $transferEmptyWeight = $sourceEmptyWeight;
                $remainingBoxes = 0;
                $newStatus = 'Cancelled';
            } else {
                $transferBoxWeight = round($sourceBoxWeight * $ratio, 2);
                $transferEmptyWeight = round($sourceEmptyWeight * $ratio, 2);
                $calculatedBoxes = (int) round((int) $source->no_of_boxes * $ratio);

                $transferBoxes = max(1, $calculatedBoxes);
                $remainingBoxes = max(1, (int) $source->no_of_boxes - $transferBoxes);

                if ((int) $source->no_of_boxes === 1) {
                    $transferBoxes = 1;
                    $remainingBoxes = 1;
                }

                $newStatus = 'Adjusted';
            }

            $transferFarmWeight = null;
            if ($source->farm_weight !== null) {
                $sourceFarmWeight = (float) $source->farm_weight;
                $transferFarmWeight = round($sourceFarmWeight * $ratio, 2);
                $remainingFarmWeight = round($sourceFarmWeight - $transferFarmWeight, 2);
            } else {
                $remainingFarmWeight = null;
            }

            // Shrink source entry
            $sourceUpdateData = [
                'no_of_boxes'  => $remainingBoxes,
                'box_weight'   => round($sourceBoxWeight - $transferBoxWeight, 2),
                'empty_weight' => round($sourceEmptyWeight - $transferEmptyWeight, 2),
                'status'       => $newStatus,
                'version'      => $source->version + 1,
            ];
            if ($source->farm_weight !== null) {
                $sourceUpdateData['farm_weight'] = $remainingFarmWeight;
            }
            $source->update($sourceUpdateData);

            // Find existing target entry (same vendor + dealer + batch + Active)
            $targetEntry = DayLoadEntry::where('batch_id', $source->batch_id)
                ->where('vendor_id', $targetVendorId)
                ->where('dealer_id', $targetDealerId)
                ->where('status', 'Active')
                ->where('id', '!=', $source->id)
                ->first();

            $newEntry = null;

            $targetCustomerRate = isset($transferData['target_customer_rate']) && $transferData['target_customer_rate'] !== '' && $transferData['target_customer_rate'] !== null
                ? (float) $transferData['target_customer_rate']
                : (float) $source->customer_rate;

            if ($targetEntry) {
                // Add to existing target entry
                $targetUpdateData = [
                    'no_of_boxes'   => (int) $targetEntry->no_of_boxes + $transferBoxes,
                    'box_weight'    => round((float) $targetEntry->box_weight + $transferBoxWeight, 2),
                    'empty_weight'  => round((float) $targetEntry->empty_weight + $transferEmptyWeight, 2),
                    'customer_rate' => $targetCustomerRate,
                    'version'       => $targetEntry->version + 1,
                ];
                if ($transferFarmWeight !== null) {
                    $targetUpdateData['farm_weight'] = round((float) ($targetEntry->farm_weight ?? 0) + $transferFarmWeight, 2);
                }
                $targetEntry->update($targetUpdateData);
            } else {
                // Create new entry for target
                $newEntry = DayLoadEntry::create([
                    'batch_id'        => $source->batch_id,
                    'vendor_id'       => $targetVendorId,
                    'dealer_id'       => $targetDealerId,
                    'paper_rate'      => $source->paper_rate,
                    'billing_rate'    => $source->billing_rate,
                    'customer_rate'   => $targetCustomerRate,
                    'no_of_boxes'     => $transferBoxes,
                    'box_weight'      => $transferBoxWeight,
                    'empty_weight'    => $transferEmptyWeight,
                    'farm_weight'     => $transferFarmWeight,
                    'status'          => 'Active',
                    'parent_entry_id' => $source->id,
                    'version'         => 1,
                    'remarks'         => $transferData['reason'] ?? null,
                ]);
            }

            // Log the transfer
            EntryAdjustmentLog::create([
                'entry_id'           => $source->id,
                'action_type'        => 'Split',
                'old_values'         => $oldValues,
                'new_values'         => [
                    'source_after'       => $source->fresh()->toArray(),
                    'target_entry'       => ($newEntry?->toArray() ?? $targetEntry->fresh()->toArray()),
                    'transferred_boxes'  => $transferBoxes,
                    'transferred_weight' => $transferWeight,
                ],
                'resulting_entry_id' => $newEntry?->id,
                'reason'             => $reason,
                'adjusted_by'        => auth()->id(),
            ]);

            $this->refreshBatchTotals($batch);

            return [$source->fresh(), $newEntry ?? $targetEntry->fresh()];
        });
    }

    /**
     * Update farm_weight on an entry. loss_weight auto-computes via the model's saving event.
     */
    public function updateFarmWeight(DayLoadEntry $entry, float $farmWeight): DayLoadEntry
    {
        $oldValues = $entry->toArray();

        $entry->update(['farm_weight' => $farmWeight]);

        EntryAdjustmentLog::create([
            'entry_id'            => $entry->id,
            'action_type'         => 'Edit',
            'old_values'          => $oldValues,
            'new_values'          => $entry->fresh()->toArray(),
            'resulting_entry_id'  => null,
            'reason'              => 'Farm weight updated',
            'adjusted_by'         => auth()->id(),
        ]);

        $this->refreshBatchTotals($entry->batch);

        return $entry->fresh();
    }

    /**
     * Update an existing day-load entry with new values.
     *
     * @throws BatchLockedException if the batch is Locked
     */
    public function updateEntry(DayLoadEntry $entry, array $data): DayLoadEntry
    {
        $batch = $entry->batch;

        if ($batch->status === 'Locked') {
            throw new BatchLockedException(
                "Cannot update entry: batch for {$batch->billing_date->format('Y-m-d')} is locked."
            );
        }

        $oldValues = $entry->toArray();

        $entry->update([
            'vendor_id'    => $data['vendor_id'],
            'dealer_id'    => $data['dealer_id'],
            'paper_rate'   => $data['paper_rate'],
            'billing_rate' => $data['billing_rate'],
            'customer_rate'=> $data['customer_rate'],
            'no_of_boxes'  => $data['no_of_boxes'],
            'box_weight'   => $data['box_weight'],
            'empty_weight' => $data['empty_weight'],
            'farm_weight'  => $data['farm_weight'] ?? null,
            'remarks'      => $data['remarks'] ?? null,
        ]);

        EntryAdjustmentLog::create([
            'entry_id'            => $entry->id,
            'action_type'         => 'Edit',
            'old_values'          => $oldValues,
            'new_values'          => $entry->fresh()->toArray(),
            'resulting_entry_id'  => null,
            'reason'              => $data['reason'] ?? 'Entry updated',
            'adjusted_by'         => auth()->id(),
        ]);

        $this->refreshBatchTotals($batch);

        return $entry->fresh();
    }

    /**
     * Finalize a batch into a DayLoadInvoice.
     *
     * Creates a new invoice if none exists, or syncs the existing one.
     * Sets batch status to 'Invoiced' and links the invoice.
     *
     * @throws BatchLockedException
     */
    public function finalizeInvoice(DayLoadBatch $batch): DayLoadInvoice
    {
        return DB::transaction(function () use ($batch) {
            if (in_array($batch->status, ['Locked', 'Invoiced'])) {
                throw new BatchLockedException(
                    "Batch for {$batch->billing_date->format('Y-m-d')} is already {$batch->status} and cannot be re-finalized."
                );
            }

            if ($batch->invoice) {
                $this->syncInvoice($batch);
                $invoice = $batch->invoice;
            } else {
                $totalAmount = (float) $batch->entries()
                    ->where('status', '!=', 'Cancelled')
                    ->sum('amount');

                $invoice = DayLoadInvoice::create([
                    'batch_id'           => $batch->id,
                    'invoice_no'         => $this->invoiceService->generateUnique('DL', 'day_load_invoices'),
                    'invoice_date'       => $batch->billing_date,
                    'total_boxes'        => $batch->total_boxes,
                    'total_box_weight'   => $batch->total_box_weight,
                    'total_empty_weight' => $batch->total_empty_weight,
                    'total_bird_weight'  => $batch->total_bird_weight,
                    'total_farm_weight'  => $batch->total_farm_weight,
                    'total_weight'       => $batch->total_weight,
                    'total_loss_weight'  => $batch->total_loss_weight,
                    'total_amount'       => round($totalAmount, 2),
                    'status'             => 'Draft',
                    'version'            => 1,
                ]);

                $batch->update([
                    'status'     => 'Invoiced',
                    'invoice_id' => $invoice->id,
                ]);
            }

            return $invoice;
        });
    }

    /**
     * Push current batch totals into the linked invoice and increment its version.
     */
    protected function syncInvoice(DayLoadBatch $batch): void
    {
        $invoice = $batch->invoice;

        if (!$invoice) {
            return;
        }

        $totalAmount = (float) $batch->entries()
            ->where('status', '!=', 'Cancelled')
            ->sum('amount');

        $invoice->update([
            'total_boxes'        => $batch->total_boxes,
            'total_box_weight'   => $batch->total_box_weight,
            'total_empty_weight' => $batch->total_empty_weight,
            'total_bird_weight'  => $batch->total_bird_weight,
            'total_farm_weight'  => $batch->total_farm_weight,
            'total_weight'       => $batch->total_weight,
            'total_loss_weight'  => $batch->total_loss_weight,
            'total_amount'       => round($totalAmount, 2),
            'version'            => $invoice->version + 1,
        ]);
    }
}

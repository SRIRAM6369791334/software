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
                COALESCE(SUM(farm_weight), 0) as total_farm_weight,
                COALESCE(SUM(loss_weight), 0) as total_loss_weight
            ')
            ->first();

        $batch->update([
            'total_boxes'        => $totals->total_boxes,
            'total_box_weight'   => $totals->total_box_weight,
            'total_empty_weight' => $totals->total_empty_weight,
            'total_bird_weight'  => $totals->total_bird_weight,
            'total_farm_weight'  => $totals->total_farm_weight,
            'total_loss_weight'  => $totals->total_loss_weight,
        ]);

        if ($batch->invoice) {
            $this->syncInvoice($batch);
        }
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
                $invoice = DayLoadInvoice::create([
                    'batch_id'           => $batch->id,
                    'invoice_no'         => $this->invoiceService->generateUnique('DL', 'day_load_invoices'),
                    'invoice_date'       => $batch->billing_date,
                    'total_boxes'        => $batch->total_boxes,
                    'total_box_weight'   => $batch->total_box_weight,
                    'total_empty_weight' => $batch->total_empty_weight,
                    'total_bird_weight'  => $batch->total_bird_weight,
                    'total_farm_weight'  => $batch->total_farm_weight,
                    'total_loss_weight'  => $batch->total_loss_weight,
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

        $invoice->update([
            'total_boxes'        => $batch->total_boxes,
            'total_box_weight'   => $batch->total_box_weight,
            'total_empty_weight' => $batch->total_empty_weight,
            'total_bird_weight'  => $batch->total_bird_weight,
            'total_farm_weight'  => $batch->total_farm_weight,
            'total_loss_weight'  => $batch->total_loss_weight,
            'version'            => $invoice->version + 1,
        ]);
    }
}

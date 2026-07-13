<?php

namespace App\Services;

use App\Models\VendorPayment;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VendorPaymentService
{
    public function paginated(
        ?string $search,
        ?int    $vendorId,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $paymentMode,
        int     $perPage = 15
    ): LengthAwarePaginator {
        return VendorPayment::with('vendor')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($nested) use ($search) {
                    $nested->whereHas('vendor', fn($v) => $v->where('firm_name', 'like', "%{$search}%"))
                           ->orWhere('reference_number', 'like', "%{$search}%");
                });
            })
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->latest('date')
            ->paginate($perPage);
    }

    public function record(array $data): VendorPayment
    {
        return DB::transaction(function () use ($data) {
            $vendor = Vendor::findOrFail($data['vendor_id']);
            
            $cashAmount = isset($data['cash_amount']) ? (float) $data['cash_amount'] : 0.00;
            $bankAmount = isset($data['bank_amount']) ? (float) $data['bank_amount'] : 0.00;
            
            $amount = round($cashAmount + $bankAmount, 2);

            $data['amount'] = $amount;
            $data['cash_amount'] = $cashAmount;
            $data['bank_amount'] = $bankAmount;
            $data['pending_balance_after'] = round($vendor->outstanding_balance - $amount, 2);
            
            $payment = VendorPayment::create($data);

            // Recalculate cash/bank ledger
            app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($payment->date));

            return $payment;
        });
    }

    public function allForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return VendorPayment::with('vendor')->orderByDesc('date')->get();
    }
}

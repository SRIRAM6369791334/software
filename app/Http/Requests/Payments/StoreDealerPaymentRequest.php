<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealerPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $cashAmount = $this->input('cash_amount');
        $bankAmount = $this->input('bank_amount');
        $amount = $this->input('amount');
        $paymentMode = $this->input('payment_mode');

        if ($cashAmount === null && $bankAmount === null && $amount === null) {
            return;
        }

        if ($cashAmount === null && $bankAmount === null && $amount !== null) {
            $amountVal = (float) $amount;
            if ($paymentMode === 'Cash') {
                $cashAmount = $amountVal;
                $bankAmount = 0.00;
            } else {
                $cashAmount = 0.00;
                $bankAmount = $amountVal;
            }
        } else {
            $cashAmount = (float) ($cashAmount ?? 0);
            $bankAmount = (float) ($bankAmount ?? 0);
            $amount = round($cashAmount + $bankAmount, 2);
        }

        $this->merge([
            'cash_amount' => $cashAmount,
            'bank_amount' => $bankAmount,
            'amount'      => $amount,
        ]);
    }

    public function rules(): array
    {
        return [
            'dealer_id'          => 'required|exists:dealers,id',
            'weekly_bill_id'     => 'nullable|exists:weekly_bills,id',
            'payment_part'       => 'nullable|in:monday,friday',
            'selected_entry_ids' => 'nullable|array',
            'selected_entry_ids.*' => 'exists:day_load_entries,id',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'amount'             => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $dealer = \App\Models\Dealer::find($this->input('dealer_id'));
                    if ($this->filled('weekly_bill_id') && $this->filled('payment_part')) {
                        $bill = \App\Models\WeeklyBill::find($this->input('weekly_bill_id'));
                        if ($bill) {
                            $expected = $this->input('payment_part') === 'monday' 
                                ? (float) $bill->monday_payment_amount 
                                : (float) $bill->friday_payment_amount;
                            if (abs((float)$value - $expected) > 0.01) {
                                $fail("Total payment must equal the expected split amount of Rs " . number_format($expected, 2));
                            }
                        }
                    } else {
                        if ($dealer && $value > $dealer->displayed_outstanding) {
                            $fail("The payout amount cannot exceed the dealer's pending balance of Rs " . number_format($dealer->displayed_outstanding, 2) . ".");
                        }
                    }
                }
            ],
            'payment_mode'       => 'required|in:Cash,UPI,NEFT,Cheque',
            'bank_transfer_type' => 'nullable|required_if:bank_amount,>0|in:UPI,Bank Transfer,NEFT,RTGS,IMPS,Cheque,Other',
            'date'               => 'required|date|before_or_equal:today',
            'notes'              => 'nullable|string|max:500',
        ];
    }
}

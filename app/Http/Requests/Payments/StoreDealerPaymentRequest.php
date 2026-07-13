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
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'amount'             => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $dealer = \App\Models\Dealer::find($this->input('dealer_id'));
                    if ($dealer && $value > $dealer->displayed_outstanding) {
                        $fail("The payout amount cannot exceed the dealer's pending balance of Rs " . number_format($dealer->displayed_outstanding, 2) . ".");
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

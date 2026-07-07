<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $codAmount = $this->input('cod_amount');
        $bankTransferAmount = $this->input('bank_transfer_amount');
        $amount = $this->input('amount');
        $paymentMode = $this->input('payment_mode');

        if ($codAmount === null && $bankTransferAmount === null && $amount === null) {
            return;
        }

        if ($codAmount === null && $bankTransferAmount === null && $amount !== null) {
            $amountVal = (float) $amount;
            if ($paymentMode === 'Cash') {
                $codAmount = $amountVal;
                $bankTransferAmount = 0.00;
            } else {
                $codAmount = 0.00;
                $bankTransferAmount = $amountVal;
            }
        } else {
            $codAmount = (float) ($codAmount ?? 0);
            $bankTransferAmount = (float) ($bankTransferAmount ?? 0);
            $amount = round($codAmount + $bankTransferAmount, 2);
            $paymentMode = ($codAmount > 0 && $bankTransferAmount <= 0) ? 'Cash' : 'Bank Transfer';
        }

        $this->merge([
            'cod_amount'           => $codAmount,
            'bank_transfer_amount' => $bankTransferAmount,
            'amount'               => $amount,
            'payment_mode'         => $paymentMode,
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_id'          => 'required|exists:customers,id',
            'cod_amount'           => 'required|numeric|min:0',
            'bank_transfer_amount' => 'required|numeric|min:0',
            'amount'               => 'required|numeric|min:0.01',
            'payment_mode'         => 'required|in:Cash,Bank Transfer',
            'payment_type'         => 'required|in:Regular,Adjustment,Opening',
            'date'                 => 'required|date|before_or_equal:today',
            'notes'                => 'nullable|string|max:500',
        ];
    }
}

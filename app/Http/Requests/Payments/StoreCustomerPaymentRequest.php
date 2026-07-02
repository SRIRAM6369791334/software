<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $codAmount = (float) $this->input('cod_amount', 0);
        $bankTransferAmount = (float) $this->input('bank_transfer_amount', 0);

        $this->merge([
            'amount' => round($codAmount + $bankTransferAmount, 2),
            'payment_mode' => $codAmount > 0 && $bankTransferAmount <= 0 ? 'Cash' : 'Bank Transfer',
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

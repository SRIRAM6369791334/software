<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealerPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'dealer_id'    => 'required|exists:dealers,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:Cash,UPI,NEFT,Cheque',
            'date'         => 'required|date|before_or_equal:today',
            'notes'        => 'nullable|string|max:500',
        ];
    }
}

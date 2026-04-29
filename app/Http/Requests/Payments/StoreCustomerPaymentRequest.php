<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id'  => 'required|exists:customers,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_mode' => 'required|in:Cash,UPI,NEFT,Cheque',
            'payment_type' => 'required|in:Full,Part,Advance',
            'date'         => 'required|date|before_or_equal:today',
            'notes'        => 'nullable|string|max:500',
        ];
    }
}

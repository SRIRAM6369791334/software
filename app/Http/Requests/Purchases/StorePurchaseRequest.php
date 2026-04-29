<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'vendor_name'    => 'required|string|max:255',
            'item'           => 'required|string|max:255',
            'quantity'       => 'required|numeric|min:0.01',
            'rate'           => 'required|numeric|min:0.01',
            'gst_percentage' => 'required|numeric|min:0|max:28',
            'payment_mode'   => 'required|in:NEFT,Cheque,UPI,Cash',
            'date'           => 'required|date|before_or_equal:today',
        ];
    }
}

<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'vendor_id'      => 'nullable|exists:vendors,id',
            'vendor_name'    => 'required|string|max:255',
            'payment_mode'   => 'required|in:NEFT,Cheque,UPI,Cash',
            'date'           => 'required|date|before_or_equal:today',
            'gst_percentage' => 'required|numeric|min:0|max:28',
            
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string|max:255',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'items.*.rate'   => 'required|numeric|min:0.01',
            'items.*.unit'   => 'nullable|string|max:20',
        ];
    }
}

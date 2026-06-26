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
            'invoice_no'     => 'nullable|string|max:255',
            'payment_mode'   => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer),Pay later(EMI)',
            'due_date'       => 'nullable|date|after_or_equal:date',
            'date'           => 'required|date|before_or_equal:today',
            'gst_percentage' => 'required|numeric|min:0|max:28',
            
            'items'                 => 'required|array|min:1',
            'items.*.item_id'       => 'nullable|exists:items,id',
            'items.*.batch_id'      => 'nullable|exists:batches,id',
            'items.*.warehouse_id'  => 'nullable|exists:warehouses,id',
            'items.*.name'          => 'required_without:items.*.item_id|string|max:255',
            'items.*.qty'           => 'required|numeric|min:0.01',
            'items.*.rate'          => 'required|numeric|min:0.01',
            'items.*.unit'          => 'nullable|string|max:20',
            'emis'                  => 'required_if:payment_mode,Pay later(EMI)|array',
            'emis.*.due_date'       => 'required_if:payment_mode,Pay later(EMI)|date',
            'emis.*.amount'         => 'required_if:payment_mode,Pay later(EMI)|numeric|min:0.01',
        ];
    }
}

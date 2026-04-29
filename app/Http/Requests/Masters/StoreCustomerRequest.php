<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|min:10|max:15',
            'address'    => 'nullable|string|max:500',
            'gst_number' => 'nullable|string|max:20',
            'route'      => 'nullable|string|max:100',
            'type'       => 'required|in:Retail,Wholesale',
        ];
    }
}

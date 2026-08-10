<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        return [
            'name'       => 'required|string|max:255',
            'phone'      => [
                'required',
                'string',
                'regex:/^[6-9]\d{9}$/',
                Rule::unique('customers', 'phone')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'address'    => 'nullable|string|max:500',
            'gst_number' => 'nullable|string|max:20',
            'route'      => 'nullable|string|max:255',
            'route_id'   => 'nullable|exists:routes,id',
            'type'       => 'required|in:Retail,Wholesale',
            'balance'    => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'The phone number field is required.',
            'phone.regex'    => 'Please enter a valid 10-digit mobile number (e.g. 9876543210).',
            'phone.unique'   => 'This phone number has already been registered.',
        ];
    }
}

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
                'min:10',
                'max:15',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'address'    => 'nullable|string|max:500',
            'gst_number' => 'nullable|string|max:20',
            'route_id'   => 'nullable|exists:routes,id',
            'type'       => 'required|in:Retail,Wholesale',
        ];
    }
}

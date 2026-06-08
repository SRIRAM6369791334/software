<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $vendorId = $this->route('vendor') ? $this->route('vendor')->id : null;

        return [
            'firm_name'      => 'required|string|max:255',
            'phone'          => [
                'required',
                'string',
                'min:10',
                'max:15',
                Rule::unique('vendors', 'phone')->ignore($vendorId),
            ],
            'contact_person' => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:20',
            'location'       => 'nullable|string|max:255',
            'route'          => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}

<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'firm_name'      => 'required|string|max:255',
            'phone'          => 'required|string|min:10|max:15',
            'contact_person' => 'nullable|string|max:255',
            'gst_number'     => 'nullable|string|max:20',
            'location'       => 'nullable|string|max:255',
            'route'          => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}

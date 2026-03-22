<?php

namespace App\Http\Requests\SupplierOrderStatuses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $supplierOrderStatus = $this->route('supplier_order_status');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('supplier_order_statuses', 'name')->ignore($supplierOrderStatus),
            ],
            'description' => 'nullable|string',
        ];
    }
}

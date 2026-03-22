<?php

namespace App\Http\Requests\SupplierOrders;

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
        $supplierOrder = $this->route('supplier_order');

        return [
            'order_number' => [
                'required',
                'string',
                Rule::unique('supplier_orders', 'order_number')->ignore($supplierOrder),
            ],
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'status_id' => 'nullable|integer|exists:supplier_order_statuses,id',
            'tracking' => 'nullable|string|max:1000',
            'ordered_at' => 'nullable|date',
            'shipped_at' => 'nullable|date',
            'arrived_at' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|decimal:0,2|min:0',
        ];
    }
}

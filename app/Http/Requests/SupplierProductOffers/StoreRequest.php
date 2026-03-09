<?php

namespace App\Http\Requests\SupplierProductOffers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        return [
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('supplier_product_offers', 'product_id')
                    ->where(fn ($query) => $query->where('supplier_id', $this->input('supplier_id'))),
            ],
            'base_cost' => 'required|decimal:0,2|min:0|max:99999999.99',
            'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
            'estimated_tax' => 'sometimes|decimal:0,2|min:0|max:99999999.99',
            'estimated_shipping' => 'sometimes|decimal:0,2|min:0|max:99999999.99',
            'other_fees' => 'sometimes|decimal:0,2|min:0|max:99999999.99',
            'is_available' => 'sometimes|boolean',
            'last_checked_at' => 'nullable|date',
        ];
    }

    protected function prepareForValidation(): void
    {
        $currency = $this->input('currency');

        $this->merge([
            'currency' => is_string($currency) ? strtoupper($currency) : $currency,
            'is_available' => $this->boolean('is_available'),
        ]);
    }
}

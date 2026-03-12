<?php

namespace App\Http\Requests\SupplierProductOffers;

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
        $offer = $this->route('supplier_product_offer');

        return [
            'priority' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('supplier_product_offers', 'priority')
                    ->where(
                        fn($query) => $query
                            ->where('supplier_id', $this->input('supplier_id'))
                            ->where('product_id', $this->input('product_id'))
                    )
                    ->ignore($offer->id),
            ],
            'base_cost' => 'required|decimal:0,2|min:0|max:99999999.99',
            'retail_price' => 'required|decimal:0,2|min:0|max:99999999.99',
            'profit_percentage' => 'required|decimal:0,4|min:0|max:1',
            'is_available' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $offer = $this->route('supplier_product_offer');

        $this->merge([
            'supplier_id' => $offer->supplier_id,
            'product_id' => $offer->product_id,
            'is_available' => $this->boolean('is_available'),
        ]);
    }
}

<?php

namespace App\Http\Requests\Products;

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
            'sku' => [
                'required', 
                'string', 
                'max:100',
                Rule::unique('products', 'sku')
            ],
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:120',
            'line' => 'nullable|string|max:120',
            'height' => 'nullable|numeric|decimal:0,3|min:0|max:999.999',
            'weight_est' => 'nullable|numeric|decimal:0,3|min:0|max:999.999',
            'weight_real' => 'nullable|numeric|decimal:0,3|min:0|max:999.999',
            'release_date' => 'nullable|date'
        ];
    }
}

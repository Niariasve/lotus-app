<?php

namespace App\Http\Requests\Suppliers;

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
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('suppliers', 'name')->ignore($this->route('supplier')->id),
            ],
            'description' => 'nullable|string',
            'priority' => 'sometimes|integer|min:0',
            'tax_policy' => 'required|decimal:0,4|min:0|max:1',
            'shipping_policy' => 'required|decimal:0,4|min:0|max:1',
            'currency' => [
                'required',
                'string',
                'size:3',
                Rule::in(['USD', 'EUR']),
            ],
        ];
    }
}

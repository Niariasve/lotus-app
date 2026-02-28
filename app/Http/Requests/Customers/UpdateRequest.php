<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'full_name' => 'required|string|max:150',
            'email' => ['nullable', 'email', Rule::unique('customers', 'email')->ignore($this->route('customer')->id)],
            'phone' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:100',

            'platform' => 'nullable|array',
            'platform.*' => 'nullable|string|max:100',
            'primary_platform' => 'nullable|string'
        ];
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->input('platform', []) as $slug => $value) {
            $attributes["platform.$slug"] = $slug;
        }

        return $attributes;
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $platforms = collect($this->input('platform', []))
                    ->filter(fn($value) => filled($value));

                if ($platforms->isNotEmpty()) {
                    if (!$this->filled('primary_platform')) {
                        $validator->errors()->add(
                            'primary_platform',
                            'You must choose a primary communication platform'
                        );
                        return;
                    }

                    if (!$platforms->has($this->input('primary_platform'))) {
                        $validator->errors()->add(
                            'primary_platform',
                            'Primary platform must have a valid platform'
                        );
                    }
                }
            }
        ];
    }
}

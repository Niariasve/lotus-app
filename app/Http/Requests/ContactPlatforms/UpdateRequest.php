<?php

namespace App\Http\Requests\ContactPlatforms;

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
                'max:50',
                Rule::unique('contact_platforms', 'slug')
                    ->ignore($this->route('contact_platform')->id)
                    ->where(
                        fn ($query) =>
                        $query->where('slug', strtolower($this->input('name')))
                    ),
            ],
            'slug' => 'required|string',
            'is_active' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => strtolower($this->input('name', '')),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}

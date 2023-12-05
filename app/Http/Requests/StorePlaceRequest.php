<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlaceRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'type' => ['required', 'string'],
            'description' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'location' => ['required', 'string'],
            'rate' => ['required', 'integer'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image']
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (currentUser()->is_admin || ($this->route('place')->user_id == currentUser()->id))
        {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string'],
            'type' => ['string'],
            'description' => ['string'],
            'phone' => ['string'],
            'location' => ['string'],
            'rate' => ['integer'],
            'category_id' => ['integer', 'exists:categories,id'],
        ];
    }
}

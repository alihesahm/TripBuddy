<?php

namespace App\Http\Requests;

use App\Enums\UserGenderEnum;
use App\Helpers\Traits\General\HasFailedValidationRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    use HasFailedValidationRequest;
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
            'name'=>['required','string','regex:/^[\p{Arabic}\p{L}\s]+$/u'],
            'email'=>['required','string','email','unique:users,email'],
            'password'=>['required','string','confirmed','min:6'],
            'gender'=>['required','string',Rule::enum(UserGenderEnum::class)]
        ];
    }
}

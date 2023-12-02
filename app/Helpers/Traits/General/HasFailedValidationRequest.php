<?php

namespace App\Helpers\Traits\General;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HasFailedValidationRequest
{
    /**
     * Handle a failed validation attempt.
     *
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            sendFailedResponse($validator->errors()->first(), $validator->errors(), 422)
        );
    }
}

<?php

namespace App\Helpers\Traits\General;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HasFailedValidationDtoRequest
{
    public static function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            throw new HttpResponseException(
                sendFailedResponse($validator->errors()->first(), null, 422)
            );
        }
    }
}

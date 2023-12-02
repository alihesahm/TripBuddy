<?php

namespace App\Helpers\Traits\General;

trait HasActivateAccount
{
    public function isVerifyEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function isNotVerifyEmail(): bool
    {
        return is_null($this->email_verified_at);
    }

    public function verifyAccount(): bool
    {
        return $this->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'code_expired_at' => null,
        ]);
    }
}

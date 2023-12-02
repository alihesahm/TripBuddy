<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomExceptions\LogicException;
use App\Http\Requests\CheckOtpRequest;
use App\Http\Requests\SendOtpRequest;
use App\Mail\ResetPasswordMail;
use App\Models\PasswordReset;
use App\Models\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    function sendOtp(SendOtpRequest $request)
    {
        $user = User::query()->where('email' ,$request->email)->first();

        $resetPassword = $user->resetPassword()->updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => generateOtp(),
                'created_at' => now(),
            ]
        );

        Mail::to($user)->send(new ResetPasswordMail($resetPassword));
        return sendSuccessResponse(data: ['email'=>$request->email]);
    }

    public function checkOtp(CheckOtpRequest $request)
    {
        $passwordRestObject = ResetPassword::query()->where([['email', $request->email], ['otp', $request->code]])->first();

        if (! $passwordRestObject) {
            return  sendFailedResponse(__('exceptions.wrong_otp'));
        }
        if (Carbon::parse($passwordRestObject->created_at)->addHours(24)->isPast()) {
            return  sendFailedResponse(__('exceptions.expired_otp'));
        }

        return $passwordRestObject;
    }
}

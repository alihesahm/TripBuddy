<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        User::create($request->validated());
        return sendSuccessResponse();
    }
    public function login(LoginRequest $request)
    {
        $user = User::where("email",$request->email)
            //->where('password',Hash::make($request->password))
            ->first();

        if (!$user || !password_verify($request->password,$user->password)){
            return sendFailedResponse(__("auth.failed"));
        }
        $user->token = $user->createToken('User Token')->plainTextToken;

        return sendSuccessResponse(message: __('auth.succeed'),data: LoginResource::make($user));
    }
}

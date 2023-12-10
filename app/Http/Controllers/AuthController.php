<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        $data = array_merge($request->validated(),['is_admin'=>false]);
        $user = User::create($data);

        $token = $user->createToken('User Token')->plainTextToken;

        return sendSuccessResponse(
            message: __('auth.succeed'),
            data: [
                'user'=>UserResource::make($user),
                'token'=>$token
            ]
        );
    }
    public function login(LoginRequest $request)
    {
        $user = User::query()
            ->where("email",$request->email)
            ->first();

        if (!$user || !password_verify($request->password,$user->password)){
            return sendFailedResponse(__("auth.failed"),status_code:200);
        }
        $token = $user->createToken('User Token')->plainTextToken;

        return sendSuccessResponse(
            message: __('auth.succeed'),
            data: [
                'user'=>UserResource::make($user),
                'token'=>$token
            ]
        );
    }

    public function logout()
    {
        currentUser()->currentAccessToken()->delete();
        return sendSuccessResponse('you logout');
    }

    public function getProfile()
    {
        $user = currentUser();
        return sendSuccessResponse(data:ProfileResource::make($user));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = currentUser();
        $data = $request->validated();
        $user->update($data);
        if($request->hasFile('image')){
            $user->addMedia($request->image)->toMediaCollection("profile");
        }
        return sendSuccessResponse(data:ProfileResource::make($user));
    }
}

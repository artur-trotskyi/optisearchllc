<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ResourceMessagesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthResetPasswordRequest;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetController extends Controller
{
    /**
     * Reset the password for the authenticated user.
     *
     * @throws ValidationException
     */
    public function resetPassword(AuthResetPasswordRequest $request): AuthResource
    {
        $user = $request->user();
        if (! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The provided current password is incorrect.',
            ]);
        }

        $user->forceFill([
            'password' => bcrypt($request->input('password')),
        ])->save();

        // Optionally revoke all tokens for security reasons
        // $user->tokens()->delete();

        return AuthResource::make([], ResourceMessagesEnum::PasswordResetSuccessful->message())
            ->setStatusCode(Response::HTTP_OK);
    }
}

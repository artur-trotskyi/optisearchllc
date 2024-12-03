<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ResourceMessagesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthVerifyRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Auth\ErrorResource;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email address.
     *
     * @unauthenticated
     */
    public function verify(AuthVerifyRequest $request): AuthResource|ErrorResource
    {
        $requestData = $request->validated();
        $user = User::find($requestData['id']);
        if (! $user) {
            return new ErrorResource([], ResourceMessagesEnum::UserNotFound->message(), Response::HTTP_NOT_FOUND);
        }

        if (! hash_equals((string) $requestData['hash'], sha1($user->getEmailForVerification()))) {
            return new ErrorResource([], ResourceMessagesEnum::InvalidHash->message(), Response::HTTP_BAD_REQUEST);
        }

        if ($user->hasVerifiedEmail()) {
            return AuthResource::make([], ResourceMessagesEnum::EmailAlreadyVerified->message());
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return AuthResource::make([], ResourceMessagesEnum::EmailVerifiedSuccessfully->message());
    }
}

<?php

namespace App\Policies;

use App\Enums\Exception\ExceptionMessagesEnum;
use App\Models\PriceSubscription;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PriceSubscriptionPolicy
{
    public function viewOrModify(?User $user, PriceSubscription $priceSubscription): Response
    {
        if ($user?->id !== $priceSubscription->user_id) {
            throw new AccessDeniedHttpException(ExceptionMessagesEnum::AuthorizationForData->message());
        }

        return Response::allow();
    }
}

<?php

namespace App\Policies;

use App\Enums\Exception\ExceptionMessagesEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OrderPolicy
{
    public function viewOrModify(?User $user, Order $order): Response
    {
        if ($user?->id !== $order->user_id) {
            throw new AccessDeniedHttpException(ExceptionMessagesEnum::AuthorizationForData->message());
        }

        return Response::allow();
    }
}

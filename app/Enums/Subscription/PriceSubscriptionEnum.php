<?php

namespace App\Enums\Subscription;

use App\Traits\EnumTrait;

enum PriceSubscriptionEnum: string
{
    use EnumTrait;

    case LIST = 'Display the list of price subscriptions.';
    case CREATED = 'Price subscription successfully created.';
    case DELETED = 'Price subscription successfully deleted.';
    case INVALID_TOKEN = 'Invalid or expired confirmation token.';
    case CONFIRMED = 'Your subscription has been confirmed!';
}

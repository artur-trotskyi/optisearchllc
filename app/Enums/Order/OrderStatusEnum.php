<?php

namespace App\Enums\Order;

use App\Traits\EnumTrait;

enum OrderStatusEnum: string
{
    use EnumTrait;

    case NEW = 'новый';
    case PROCESSING = 'в обработке';
    case SENT = 'отправлен';
    case DELIVERED = 'доставлен';
    case DELETED = 'удалён';
}

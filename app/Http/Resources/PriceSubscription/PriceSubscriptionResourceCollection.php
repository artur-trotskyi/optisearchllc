<?php

namespace App\Http\Resources\PriceSubscription;

use App\Http\Resources\BaseResourceCollection;

class PriceSubscriptionResourceCollection extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'success' => true,
            'message' => $this->additional['message'] ?? null,
        ];
    }
}

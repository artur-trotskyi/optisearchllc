<?php

namespace App\Dto\Subscription\PriceSubscription;

use App\Traits\MakeableTrait;

final readonly class PriceSubscriptionStoreDto
{
    use MakeableTrait;

    public int $user_id;

    public string $url;

    public string $email;

    public ?float $price;

    /**
     * OrderStoreDto constructor.
     *
     * @param  array  $data  An associative array with data for store price subscription.
     */
    public function __construct(array $data)
    {
        $this->user_id = auth()->id();
        $this->url = $data['url'];
        $this->email = $data['email'];
        $this->price = null;
    }
}

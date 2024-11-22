<?php

namespace App\Dto\Order;

use App\Traits\MakeableTrait;

final readonly class OrderStoreDto
{
    use MakeableTrait;

    public int $user_id;

    public string $product_name;

    public float $amount;

    public string $status;

    /**
     * OrderStoreDto constructor.
     *
     * @param  array  $data  An associative array with data for store order.
     */
    public function __construct(array $data)
    {
        $this->user_id = auth()->id();
        $this->product_name = $data['product_name'];
        $this->amount = $data['amount'];
        $this->status = $data['status'];
    }
}

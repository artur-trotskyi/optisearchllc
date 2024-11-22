<?php

namespace App\Dto\Order;

use App\Traits\MakeableTrait;

final readonly class OrderFilterDto
{
    use MakeableTrait;

    public ?string $q;

    public int $itemsPerPage;

    public int $page;

    public ?string $product_name;

    public ?string $status;

    public ?string $sortBy;

    public ?string $orderBy;

    /**
     * OrderFilterDto constructor.
     *
     * @param  array  $data  An associative array with data for filtering orders.
     */
    public function __construct(array $data)
    {
        $this->q = $data['q'] ?? null;
        $this->itemsPerPage = $data['itemsPerPage'];
        $this->page = $data['page'];
        $this->product_name = $data['product_name'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->sortBy = $data['sortBy'] ?? null;
        $this->orderBy = $data['orderBy'] ?? null;
    }
}

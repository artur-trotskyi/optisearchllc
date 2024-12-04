<?php

namespace App\Dto\Product;

use App\Traits\MakeableTrait;

final readonly class ProductFilterDto
{
    use MakeableTrait;

    public ?string $q;

    public int $itemsPerPage;

    public int $page;

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
        $this->sortBy = $data['sortBy'] ?? null;
        $this->orderBy = $data['orderBy'] ?? null;
    }
}

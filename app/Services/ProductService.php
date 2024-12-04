<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService extends BaseService
{
    /**
     * Create a new OrderService instance.
     *
     * @param  ProductRepository  $repo  The repository for managing orders.
     */
    public function __construct(ProductRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Filter and paginate products based on various criteria.
     */
    public function filter($orderFilterDto): array
    {
        $q = $orderFilterDto->q;
        $itemsPerPage = $orderFilterDto->itemsPerPage;
        $page = $orderFilterDto->page;
        $sortBy = $orderFilterDto->sortBy;
        $orderBy = $orderFilterDto->orderBy;

        $orders = $this->repo->getFilteredWithPaginate($q, $itemsPerPage, $page, $sortBy, $orderBy);

        return [
            'items' => $orders->items(),
            'totalPages' => $orders->total() === 0 ? 0 : $orders->lastPage(),
            'totalItems' => $orders->total(),
            'page' => $orders->currentPage(),
        ];
    }
}

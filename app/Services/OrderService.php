<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Cache;

class OrderService extends BaseService
{
    /**
     * Create a new OrderService instance.
     *
     * @param  OrderRepository  $repo  The repository for managing orders.
     */
    public function __construct(OrderRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Filter and paginate orders based on various criteria.
     */
    public function filter(
        ?string $q, int $itemsPerPage, int $page, array $strictFilters, ?string $sortBy, ?string $orderBy): array
    {
        $cacheTag = config('cache.tags.orders');
        $filtersQueryString = http_build_query($strictFilters);
        $cacheKey = "q={$q}&itemsPerPage={$itemsPerPage}&page={$page}&{$filtersQueryString}&sortBy={$sortBy}&orderBy={$orderBy}";
        $orders = Cache::tags($cacheTag)->remember($cacheKey, config('cache.ttl'), function () use ($q, $itemsPerPage, $page, $strictFilters, $sortBy, $orderBy) {
            return $this->repo->getFilteredWithPaginate($q, $itemsPerPage, $page, $strictFilters, $sortBy, $orderBy);
        });

        return [
            'items' => $orders->items(),
            'totalPages' => $orders->total() === 0 ? 0 : $orders->lastPage(),
            'totalItems' => $orders->total(),
            'page' => $orders->currentPage(),
        ];
    }

    /**
     * Delete record by id.
     */
    public function destroy(string $id): bool
    {
        return $this->repo->destroy($id);
    }
}

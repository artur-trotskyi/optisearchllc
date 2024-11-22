<?php

namespace App\Repositories;

use App\Enums\Order\OrderStatusEnum;
use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * Repo Constructor
     * Override to clarify typehinted model.
     *
     * @param  Order  $model  Repo DB ORM Model
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getFilteredWithPaginate(
        ?string $q, int $itemsPerPage, int $page, array $strictFilters, ?string $sortBy = null, ?string $orderBy = null): LengthAwarePaginator
    {
        $query = $this->model
            ->when($q, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('product_name', 'like', '%'.$search.'%');
                });
            })
            ->when(! empty($strictFilters['product_name']), function ($query) use ($strictFilters): void {
                $query->where('product_name', $strictFilters['product_name']);
            })
            ->when(! empty($strictFilters['status']), function ($query) use ($strictFilters): void {
                $query->where('status', $strictFilters['status']);
            })
            ->currentUser();

        if ($sortBy && $orderBy) {
            $query->orderBy($sortBy, $orderBy);
        }
        $query->orderBy($this->sortBy, $this->sortOrder);

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    /**
     * Remove record from the database.
     */
    public function destroy(string $id): bool
    {
        return (bool) DB::transaction(function () use ($id) {
            $this->model->update(['status' => OrderStatusEnum::DELETED->message()]);

            return $this->model->destroy($id);
        });

    }
}

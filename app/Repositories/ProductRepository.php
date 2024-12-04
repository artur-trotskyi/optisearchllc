<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository
{
    /**
     * Repo Constructor
     * Override to clarify typehinted model.
     *
     * @param  Product  $model  Repo DB ORM Model
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getFilteredWithPaginate(
        ?string $q, int $itemsPerPage, int $page, ?string $sortBy = null, ?string $orderBy = null): LengthAwarePaginator
    {
        $query = $this->model
            ->when($q, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%');
                });
            });

        if ($sortBy && $orderBy) {
            $query->orderBy($sortBy, $orderBy);
        }
        $query->orderBy($this->sortBy, $this->sortOrder);

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }
}

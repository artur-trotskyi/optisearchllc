<?php

namespace App\Repositories;

use App\Models\PriceSubscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PriceSubscriptionRepository extends BaseRepository
{
    /**
     * Repo Constructor
     * Override to clarify typehinted model.
     *
     * @param  PriceSubscription  $model  Repo DB ORM Model
     */
    public function __construct(PriceSubscription $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all instances of model.
     */
    public function getAllForCurrentUser(): Collection
    {
        return $this->model
            ->currentUser()
            ->orderBy($this->sortBy, $this->sortOrder)
            ->get();
    }

    /**
     * Get paginated instances of model for the current user.
     */
    public function getAllForCurrentUserPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->currentUser()
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate($perPage);
    }

    /**
     * Get all confirmed instances of the model.
     */
    public function getAllConfirmed(): Collection
    {
        return $this->model
            ->where('is_confirmed', true)
            ->get();
    }
}

<?php

namespace App\Services\Subscription\PriceSubscription;

use App\Repositories\PriceSubscriptionRepository;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BasePriceSubscriptionService extends BaseService
{
    /**
     * Create a new BasePriceSubscriptionService instance.
     *
     * @param  PriceSubscriptionRepository  $repo  The repository for managing price subscriptions.
     */
    public function __construct(PriceSubscriptionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllForCurrentUser(): Collection
    {
        return $this->repo->getAllForCurrentUser();
    }

    public function getAllForCurrentUserPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->getAllForCurrentUserPaginated($perPage);
    }

    public function getAllConfirmed(): Collection
    {
        return $this->repo->getAllConfirmed();
    }
}

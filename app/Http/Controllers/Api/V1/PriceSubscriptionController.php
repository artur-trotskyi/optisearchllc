<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Subscription\PriceSubscriptionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\PriceSubscription\PriceSubscriptionIndexRequest;
use App\Http\Requests\Subscription\PriceSubscription\PriceSubscriptionStoreRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\PriceSubscription\PriceSubscriptionResourceCollection;
use App\Models\PriceSubscription;
use App\Services\Subscription\PriceSubscription\BasePriceSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Random\RandomException;

class PriceSubscriptionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly BasePriceSubscriptionService $priceSubscriptionService,
    ) {}

    /**
     * Display the list of price subscriptions.
     */
    public function index(PriceSubscriptionIndexRequest $request): PriceSubscriptionResourceCollection
    {
        $perPage = $request->query('per_page', 10);
        $priceSubscriptions = $this->priceSubscriptionService->getAllForCurrentUserPaginated($perPage);

        return PriceSubscriptionResourceCollection::make($priceSubscriptions)
            ->withStatusMessage(true, PriceSubscriptionEnum::LIST->message());
    }

    /**
     * Add a new price subscription.
     *
     * @throws RandomException
     */
    public function store(PriceSubscriptionStoreRequest $request): BaseResource
    {
        $priceSubscriptionDto = $request->getDto();

        $confirmationToken = bin2hex(random_bytes(16));
        $priceSubscriptionData = array_merge((array) $priceSubscriptionDto, [
            'confirmation_token' => $confirmationToken,
            'is_confirmed' => false,
        ]);

        $newPriceSubscription = $this->priceSubscriptionService->create($priceSubscriptionData);

        return BaseResource::make($newPriceSubscription)
            ->withStatusMessage(true, PriceSubscriptionEnum::CREATED->message());
    }

    /**
     * Delete a price subscription.
     */
    public function destroy(PriceSubscription $priceSubscription): BaseResource
    {
        Gate::authorize('viewOrModify', $priceSubscription);

        $this->priceSubscriptionService->destroy($priceSubscription->getAttribute('id'));

        return BaseResource::make([])
            ->withStatusMessage(true, PriceSubscriptionEnum::DELETED->message());
    }

    /**
     * Confirm the price subscription using a confirmation token.
     *
     * @unauthenticated
     */
    public function confirm(Request $request): BaseResource
    {
        $token = $request->query('token');
        $priceSubscription = PriceSubscription::where('confirmation_token', $token)->first();
        if (! $priceSubscription) {
            return BaseResource::make([])
                ->withStatusMessage(false, PriceSubscriptionEnum::INVALID_TOKEN->message());
        }

        // Update the subscription without triggering observers
        $priceSubscription->updateQuietly([
            'is_confirmed' => true,
            'confirmation_token' => null,
        ]);

        return BaseResource::make($priceSubscription)
            ->withStatusMessage(true, PriceSubscriptionEnum::CONFIRMED->message());
    }
}

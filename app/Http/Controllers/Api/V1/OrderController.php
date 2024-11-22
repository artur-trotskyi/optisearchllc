<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ResourceMessagesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderFilterRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller implements HasMiddleware
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewOrModify,order', only: ['update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OrderFilterRequest $request): OrderCollection
    {
        $orderFilterDto = $request->getDto();

        $q = $orderFilterDto->q;
        $itemsPerPage = $orderFilterDto->itemsPerPage;
        $page = $orderFilterDto->page;
        $product_name = $orderFilterDto->product_name;
        $status = $orderFilterDto->status;
        $sortBy = $orderFilterDto->sortBy;
        $orderBy = $orderFilterDto->orderBy;

        $orders = $this->orderService->filter($q, $itemsPerPage, $page, ['product_name' => $product_name, 'status' => $status], $sortBy, $orderBy);

        return (new OrderCollection($orders))
            ->withStatusMessage(true, ResourceMessagesEnum::DataRetrievedSuccessfully->message());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request): OrderResource
    {
        $orderStoreDto = $request->getDto();
        $newOrder = $this->orderService->create([
            'user_id' => $orderStoreDto->user_id,
            'product_name' => $orderStoreDto->product_name,
            'amount' => $orderStoreDto->amount,
            'status' => $orderStoreDto->status,
        ]);

        return (new OrderResource($newOrder))
            ->withStatusMessage(true, ResourceMessagesEnum::DataCreatedSuccessfully->message());
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): OrderResource
    {
        Gate::authorize('viewOrModify', $order);

        return (new OrderResource($order))
            ->withStatusMessage(true, ResourceMessagesEnum::DataRetrievedSuccessfully->message());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order): OrderResource
    {
        $orderUpdateDto = $request->getDto();
        $this->orderService->update($order->getAttribute('id'), [
            'user_id' => $orderUpdateDto->user_id,
            'product_name' => $orderUpdateDto->product_name,
            'amount' => $orderUpdateDto->amount,
            'status' => $orderUpdateDto->status,
        ]);

        return (new OrderResource([]))
            ->withStatusMessage(true, ResourceMessagesEnum::DataUpdatedSuccessfully->message());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): OrderResource
    {
        $this->orderService->destroy($order->getAttribute('id'));

        return (new OrderResource([]))
            ->withStatusMessage(true, ResourceMessagesEnum::DataDeletedSuccessfully->message());
    }
}

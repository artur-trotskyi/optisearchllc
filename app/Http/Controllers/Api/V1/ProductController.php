<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ResourceMessagesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductFilterRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Services\ProductService;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ProductFilterRequest $request): ProductCollection
    {
        $productFilterDto = $request->getDto();
        $orders = $this->productService->filter($productFilterDto);

        return (new ProductCollection($orders))
            ->withStatusMessage(true, ResourceMessagesEnum::DataRetrievedSuccessfully->message());
    }
}

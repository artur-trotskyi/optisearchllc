<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\BaseResourceCollection;
use Illuminate\Http\Request;

class OrderCollection extends BaseResourceCollection
{
    public static $wrap = null;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->collection['items'];

        return [
            'orders' => $items,
            'totalPages' => $this->resource['totalPages']->resource,
            'totalItems' => $this->resource['totalItems']->resource,
            'items' => count($items->resource),
            'page' => $this->resource['page']->resource,
        ];
    }
}

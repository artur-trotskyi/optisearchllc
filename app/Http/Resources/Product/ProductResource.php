<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class ProductResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}

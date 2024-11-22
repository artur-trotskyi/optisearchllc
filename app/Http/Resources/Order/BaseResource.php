<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected bool $success = true;

    protected string $message = '';

    /**
     * Set success and message properties.
     */
    public function withStatusMessage(bool $success, string $message): self
    {
        $this->success = $success;
        $this->message = $message;

        return $this;
    }

    /**
     * Customize the response for the resource.
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setData([
            'success' => $this->success,
            'message' => $this->message,
            'data' => $response->getData()->data,
        ]);
    }
}

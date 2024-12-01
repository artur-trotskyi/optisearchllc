<?php

namespace App\Http\Requests\Subscription\PriceSubscription;

use App\Dto\Subscription\PriceSubscription\PriceSubscriptionStoreDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriceSubscriptionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:255', Rule::unique('price_subscriptions')->where(function ($query) {
                return $query->where('user_id', auth()->id());
            })],
            'email' => ['required', 'email'],
        ];
    }

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     *
     * @return PriceSubscriptionStoreDto A DTO with the validated price subscription store data.
     */
    public function getDto(): PriceSubscriptionStoreDto
    {
        return PriceSubscriptionStoreDto::make($this->validated());
    }
}

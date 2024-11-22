<?php

namespace App\Http\Requests\Order;

use App\Dto\Order\OrderStoreDto;
use app\Enums\Order\OrderStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
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
            'product_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', Rule::in(OrderStatusEnum::cases())],
        ];
    }

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     *
     * @return OrderStoreDto A DTO with the validated order store data.
     */
    public function getDto(): OrderStoreDto
    {
        return OrderStoreDto::make($this->validated());
    }
}

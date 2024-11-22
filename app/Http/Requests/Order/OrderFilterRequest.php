<?php

namespace App\Http\Requests\Order;

use App\Dto\Order\OrderFilterDto;
use App\Enums\Order\OrderFilterEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderFilterRequest extends FormRequest
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
            'q' => ['sometimes', 'nullable', 'string'],
            /**
             * @example 20
             */
            'itemsPerPage' => ['required', 'integer', 'between:'.OrderFilterEnum::itemsPerPage()['min'].','.OrderFilterEnum::itemsPerPage()['max']],
            /**
             * @example 1
             */
            'page' => ['required', 'integer', 'min:1'],
            'product_name' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'string'],
            'sortBy' => ['sometimes', 'nullable', 'string', Rule::in(OrderFilterEnum::sortableFields())],
            'orderBy' => ['sometimes', 'nullable', 'string', Rule::in(OrderFilterEnum::sortOrderOptions())],
        ];
    }

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     *
     * @return OrderFilterDto A DTO with the validated order filter data.
     */
    public function getDto(): OrderFilterDto
    {
        return OrderFilterDto::make($this->validated());
    }
}

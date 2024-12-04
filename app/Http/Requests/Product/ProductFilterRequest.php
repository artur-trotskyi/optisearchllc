<?php

namespace App\Http\Requests\Product;

use App\Dto\Product\ProductFilterDto;
use App\Enums\Product\ProductFilterEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
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
            'itemsPerPage' => ['required', 'integer', 'between:'.ProductFilterEnum::itemsPerPage()['min'].','.ProductFilterEnum::itemsPerPage()['max']],
            /**
             * @example 1
             */
            'page' => ['required', 'integer', 'min:1'],
            'sortBy' => ['sometimes', 'nullable', 'string', Rule::in(ProductFilterEnum::sortableFields())],
            'orderBy' => ['sometimes', 'nullable', 'string', Rule::in(ProductFilterEnum::sortOrderOptions())],
        ];
    }

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     *
     * @return ProductFilterDto A DTO with the validated order filter data.
     */
    public function getDto(): ProductFilterDto
    {
        return ProductFilterDto::make($this->validated());
    }
}

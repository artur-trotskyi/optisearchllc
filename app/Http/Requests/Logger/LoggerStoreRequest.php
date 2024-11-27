<?php

namespace App\Http\Requests\Logger;

use App\Dto\Logger\LoggerStoreDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoggerStoreRequest extends FormRequest
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
            'message' => ['required', 'string', 'max:200'],
        ];
    }

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     *
     * @return LoggerStoreDto A DTO with the validated logger store data.
     */
    public function getDto(): LoggerStoreDto
    {
        return LoggerStoreDto::make($this->validated());
    }
}

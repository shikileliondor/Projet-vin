<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageProducts() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['is_active' => ['required', 'boolean']];
    }
}

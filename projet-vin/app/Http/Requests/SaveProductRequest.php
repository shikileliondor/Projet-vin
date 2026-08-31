<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageProducts() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'vintage' => ['nullable', 'integer', 'min:1900', 'max:'.((int) date('Y') + 1)],
            'volume' => ['required', 'string', 'max:50'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'bottles_per_case' => ['required', 'integer', 'min:1', 'max:1000'],
            'minimum_stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')->ignore($this->route('product')),
            ],
            'photo' => ['nullable', File::image()->max(2 * 1024)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

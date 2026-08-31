<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageInventories() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.physical_quantity' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['items', 'items.*.product_id'])) {
                return;
            }

            $submittedIds = [];

            foreach ((array) $this->input('items', []) as $item) {
                if (is_array($item) && array_key_exists('product_id', $item)) {
                    $submittedIds[] = (int) $item['product_id'];
                }
            }

            sort($submittedIds);
            $activeIds = Product::query()->where('is_active', true)->pluck('id')->sort()->values()->all();

            if ($submittedIds !== $activeIds) {
                $validator->errors()->add('items', 'Tous les produits actifs doivent être comptés. Rechargez la page.');
            }
        }];
    }
}

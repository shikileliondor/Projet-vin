<?php

namespace App\Http\Requests;

use App\Enums\StockMovementReason;
use App\Enums\StockUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockExitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canRecordExits() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'product_id' => ['required', Rule::exists('products', 'id')->where('is_active', true)],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::enum(StockUnit::class)],
            'reason' => ['required', Rule::enum(StockMovementReason::class), Rule::notIn([
                StockMovementReason::Purchase->value,
                StockMovementReason::Inventory->value,
            ])],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

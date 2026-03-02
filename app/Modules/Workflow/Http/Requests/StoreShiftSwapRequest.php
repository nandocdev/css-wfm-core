<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreShiftSwapRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'target_id' => ['required', 'integer', 'exists:employees,id'],
            'swap_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}

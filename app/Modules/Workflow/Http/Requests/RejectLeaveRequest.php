<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RejectLeaveRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void {
        $comments = $this->input('comments');

        $this->merge([
            'comments' => is_string($comments) ? trim(strip_tags($comments)) : $comments,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'comments' => ['required', 'string', 'max:1000'],
        ];
    }
}

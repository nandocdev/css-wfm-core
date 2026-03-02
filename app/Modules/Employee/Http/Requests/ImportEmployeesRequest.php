<?php

declare(strict_types=1);

namespace App\Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ImportEmployeesRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'file.required' => 'Debes seleccionar un archivo CSV para importar.',
            'file.file' => 'El archivo de importación no es válido.',
            'file.mimes' => 'El archivo debe tener formato CSV.',
            'file.max' => 'El archivo no debe superar los 4 MB.',
        ];
    }
}

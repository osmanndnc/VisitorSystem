<?php

namespace App\Http\Requests;

use App\Rules\ValidTcNo;
use Illuminate\Foundation\Http\FormRequest;

class VisitUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tc_no'           => ['required', 'string', new ValidTcNo()],
            'name'            => ['required', 'string'],
            'phone'           => ['required', 'string'],
            'plate'           => ['nullable', 'string', 'max:20'],
            'department_id'   => ['required', 'integer', 'exists:departments,id'], // ✅ zorunlu
            'person_to_visit' => ['nullable', 'string'], 
            'purpose'         => ['required', 'string'],
            'purpose_note'    => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'plate' => ($this->input('plate') === '') ? null : $this->input('plate'),
        ]);
    }
}

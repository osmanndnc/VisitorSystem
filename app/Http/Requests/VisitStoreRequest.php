<?php

namespace App\Http\Requests;

use App\Rules\ValidTcNo;
use Illuminate\Foundation\Http\FormRequest;

class VisitStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gerekirse policy ile sınırlandırılabilir.
        return true;
    }

    public function rules(): array
    {
        return [
            'tc_no'           => ['required', 'string', new ValidTcNo()],
            'name'            => ['required', 'string'],
            'phone'           => ['required', 'string'],
            'plate'           => ['nullable', 'string', 'max:20'], // opsiyonel
            'person_to_visit' => ['required', 'string'],
            'purpose'         => ['required', 'string'],
            'purpose_note'    => ['nullable','string','max:500'],
        ];
    }

    /**
     * Kullanıcı plaka alanını boş bıraktığında "" gelir.
     * DB tarafında nullable kolona uyum için null'a çeviriyoruz.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'plate' => ($this->input('plate') === '') ? null : $this->input('plate'),
        ]);
    }
}

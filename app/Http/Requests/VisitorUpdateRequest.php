<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidTcNo;

class VisitorUpdateRequest extends FormRequest
{
    /**
     * Güncelleme işlemi için geçerli olan kurallar.
     */
    public function rules()
    {
        return [
            'tc_no' => ['required', 'string', new ValidTcNo()], // T.C. No kontrolü 
            'name' => 'required|string',                        // İsim boş olamaz
            'phone' => 'required|string',                       // Telefon boş olamaz
            'plate' => 'required|string',                       // Plaka boş olamaz
            'person_to_visit' => 'required|string',             // Ziyaret edilecek kişi seçilmeli
            'purpose' => 'required|string',                     // Amaç belirtilmeli
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidTcNo;

class VisitorStoreRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'tc_no' => ['required', 'string', new ValidTcNo()],
            'name' => 'required|string',
            'phone' => 'required|string',
            'plate' => 'required|string',
            'person_to_visit' => 'required|string',
            'purpose' => 'required|string',
        ];
    }
}

<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTcNo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 11 haneli mi ve 0 ile başlamıyor mu?
        if (!preg_match('/^[1-9][0-9]{10}$/', $value)) {
            $fail('T.C. Kimlik Numarası 11 haneli olmalı ve 0 ile başlamamalıdır.');
            return;
        }

        $digits = array_map('intval', str_split($value));

        $sumOdd  = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
        $sumEven = $digits[1] + $digits[3] + $digits[5] + $digits[7];

        $digit10 = (($sumOdd * 7) - $sumEven) % 10;
        $digit11 = array_sum(array_slice($digits, 0, 10)) % 10;

        if ($digits[9] !== $digit10 || $digits[10] !== $digit11) {
            $fail('Geçerli bir T.C. Kimlik Numarası giriniz.');
        }
    }
}

<?php

namespace App\Helpers;

class MaskHelper
{
    public static function fullMask($text)
    {
        return str_repeat('*', mb_strlen((string)$text));
    }

    public static function partialMask($text, $visibleStart = 1, $visibleEnd = 1)
    {
        if (!$text) return '';
        $text = (string)$text;
        $length = mb_strlen($text);
        if ($length <= $visibleStart + $visibleEnd) {
            return str_repeat('*', $length);
        }
        $start  = mb_substr($text, 0, $visibleStart);
        $end    = $visibleEnd > 0 ? mb_substr($text, -$visibleEnd) : '';
        $middle = str_repeat('*', $length - $visibleStart - $visibleEnd);
        return $start . $middle . $end;
    }

    /* ===== Alan özel maskeler ===== */

    // Ad Soyad (z**** ç****)
    public static function maskName($fullName)
    {
        if (!$fullName) return '';
        $parts = preg_split('/\s+/u', trim((string)$fullName));
        $masked = array_map(function ($p) {
            $len = mb_strlen($p);
            return $len > 0 ? (mb_substr($p, 0, 1) . str_repeat('*', max($len - 1, 0))) : '';
        }, $parts);
        return implode(' ', $masked);
    }

    // T.C. No: baştan 1, sondan 2 açık  -> 1*******46
    public static function maskTc($tc)
    {
        $digits = preg_replace('/\D+/', '', (string)$tc);
        if ($digits === '') return '';
        return self::partialMask($digits, 1, 2);
    }

    // Telefon: sadece sondan 2 açık  -> **********55
    // Eğer 4 açık istiyorsan alttaki $visibleEnd'i 4 yap.
    public static function maskPhone($phone, int $visibleEnd = 2)
    {
        $digits = preg_replace('/\D+/', '', (string)$phone);
        if ($digits === '') return '';
        $len = mb_strlen($digits);
        if ($visibleEnd >= $len) {
            return str_repeat('*', $len);
        }
        return str_repeat('*', $len - $visibleEnd) . mb_substr($digits, -$visibleEnd);
    }

    // Plaka: sadece ilk iki (il kodu) açık, geri kalan tamamen maskeli
    // Örn: 34 ABC 1234 -> 34 ** ****  |  34 AB 34 -> 34 ** **
    public static function maskPlate($plate)
    {
        $p = mb_strtoupper(trim((string)$plate), 'UTF-8');
        if ($p === '') return '';
        // Türkiye plaka formatları
        if (preg_match('/^(\d{2})\s*([A-ZÇĞİÖŞÜ]{1,3})\s*(\d{2,4})$/u', $p, $m)) {
            $il      = $m[1];
            $letters = str_repeat('*', mb_strlen($m[2]));   // harflerin tamamı *
            $nums    = str_repeat('*', mb_strlen($m[3]));   // rakamların tamamı *
            return trim($il.' '.$letters.' '.$nums);
        }
        // Bilinmeyen format: sadece ilk 2 karakteri açık bırak
        return self::partialMask($p, 2, 0);
    }

    // Alan adına göre uygula
    public static function apply(string $field, $value, array $maskedFields)
    {
        if (!in_array($field, $maskedFields, true)) {
            return $value ?? '-';
        }

        return match ($field) {
            'name'            => self::maskName($value),
            'tc_no'           => self::maskTc($value),
            'phone'           => self::maskPhone($value, 2),   // istersen 4 yap
            'plate'           => self::maskPlate($value),
            'person_to_visit' => self::maskName($value),
            default           => self::partialMask((string)$value, 1, 1),
        };
    }

    // Visit koleksiyonunu seçilen alanlara göre düzleştir + maskele
    public static function maskVisits($visits, array $fields, array $masked = [])
    {
        return $visits->map(function ($visit) use ($fields, $masked) {
            $row = [];

            foreach ($fields as $field) {
                $val = match ($field) {
                    'id'               => $visit->id,
                    'entry_time'       => $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-',
                    'name'             => optional($visit->visitor)->name ?? '-',
                    'tc_no'            => optional($visit->visitor)->tc_no ?? '-',
                    'phone'            => $visit->phone ?? '-',
                    'plate'            => $visit->plate ?? '-',
                    'purpose'          => $visit->purpose ?? '-',
                    'person_to_visit'  => $visit->person_to_visit ?? '-',
                    'approved_by'      => optional($visit->approver)->ad_soyad ?? ($visit->approved_by ?? '-'),
                    default            => $visit->$field ?? '-',
                };

                $row[$field] = self::apply($field, $val, $masked);
            }

            return $row;
        });
    }
}

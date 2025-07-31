<?php

namespace App\Helpers;

class MaskHelper
{
    public static function fullMask($text)
    {
        return str_repeat('*', mb_strlen($text));
    }

    public static function partialMask($text, $visibleStart = 1, $visibleEnd = 1)
    {
        if (!$text) return '';
        $length = mb_strlen($text);
        if ($length <= $visibleStart + $visibleEnd) {
            return str_repeat('*', $length);
        }
        $start = mb_substr($text, 0, $visibleStart);
        $end = mb_substr($text, -$visibleEnd);
        $middle = str_repeat('*', $length - $visibleStart - $visibleEnd);
        return $start . $middle . $end;
    }

    public static function maskName($fullName)
    {
        if (!$fullName) return '';
        $parts = explode(' ', $fullName);
        $maskedParts = array_map(function ($part) {
            return mb_substr($part, 0, 1) . str_repeat('*', max(mb_strlen($part) - 1, 0));
        }, $parts);
        return implode(' ', $maskedParts);
    }

    public static function maskPlate($plate)
    {
        if (!$plate) return '';
        if (preg_match('/^(\d{2})\s*(\D+)\s*(\d+)$/', $plate, $matches)) {
            return $matches[1] . ' ' . str_repeat('*', mb_strlen($matches[2])) . ' ' . str_repeat('*', mb_strlen($matches[3]));
        }
        return self::partialMask($plate, 2, 2);
    }

    /**
     * Ziyaret kayıtlarını maskeler ve array olarak döner.
     * $visits: Collection
     * $fields: string[]
     * 
     * Return: Collection of arrays with masked values
     */
    public static function maskVisits($visits, $fields)
    {
        return $visits->map(function ($visit) use ($fields) {
            $visitor = $visit->visitor ?? null;
            $row = [];

            // ID ve approved_by her zaman ekleyelim (maskelenmeyecek)
            $row['id'] = $visit->id;
            $row['approved_by'] = $visit->approver->username ?? $visit->approved_by ?? '-';

            foreach ($fields as $field) {
                if (in_array($field, ['id', 'approved_by'])) {
                    // Zaten eklendi yukarıda
                    continue;
                }

                switch ($field) {
                    case 'entry_time':
                        $row['entry_time'] = $visit->entry_time ? $visit->entry_time->format('Y-m-d H:i:s') : '-';
                        break;
                    case 'name':
                        $row['name'] = $visitor ? self::maskName($visitor->name) : '-';
                        break;
                    case 'tc_no':
                        $row['tc_no'] = $visitor ? self::partialMask($visitor->tc_no, 1, 2) : '-';
                        break;
                    case 'phone':
                        $row['phone'] = $visitor ? self::partialMask($visitor->phone, 0, 2) : '-';
                        break;
                    case 'plate':
                        $row['plate'] = $visitor ? self::maskPlate($visitor->plate) : '-';
                        break;
                    case 'purpose':
                        $row['purpose'] = $visit->purpose ?? '-';
                        break;
                    case 'person_to_visit':
                        $row['person_to_visit'] = $visit->person_to_visit ? self::maskName($visit->person_to_visit) : '-';
                        break;
                    default:
                        $row[$field] = $visit->$field ?? '-';
                        break;
                }
            }

            return $row;
        });
    }
}

<?php

namespace App\Helpers;

class ExcelHelper
{
    public static function getHighestColumnFromMultiRow(array $row)
    {
        $max = 0;
        foreach ($row as $column) {
            if ($max < count($column)) {
                $max = count($column);
            }
        }
        return $max;
    }

    public static function numberToAlphabet(int $num): string
    {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return self::numberToAlphabet($num2) . $letter;
        } else {
            return $letter;
        }
    }
}
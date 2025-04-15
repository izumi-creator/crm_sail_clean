<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    public static function format(?string $number): string
    {
        if (empty($number)) return '';

        // ハイフン除去
        $number = preg_replace('/[^0-9]/', '', $number);

        // 携帯・IP電話（11桁）
        if (preg_match('/^(050|070|080|090)(\d{4})(\d{4})$/', $number, $matches)) {
            return "{$matches[1]}-{$matches[2]}-{$matches[3]}";
        }

        // フリーダイヤル（0120/0800）
        if (preg_match('/^(0120|0800)(\d{2})(\d{4})$/', $number, $matches)) {
            return "{$matches[1]}-{$matches[2]}-{$matches[3]}";
        }

        // 関東地方 固定電話（辞書ベース）
        $kantoAreaCodes = [
            '03',    // 東京23区
            '042',   // 多摩
            '043',   // 千葉市
            '044',   // 川崎
            '045',   // 横浜
            '046',   // 湘南・横須賀
            '047',   // 船橋・松戸
            '048',   // さいたま
            '049',   // 川越
        ];

        foreach ($kantoAreaCodes as $code) {
            if (str_starts_with($number, $code)) {
                $rest = substr($number, strlen($code));
                if (strlen($rest) === 7) {
                    return $code . '-' . substr($rest, 0, 3) . '-' . substr($rest, 3);
                }
            }
        }

        // fallback：整形できない場合そのまま返す
        return $number;
    }
}
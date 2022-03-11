<?php


use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\ArrayShape;

if (!function_exists('usdToTry')) {
    function usdToTry(): float|bool
    {
        try {
            $response = Cache::remember('phone_codes-' . date('Ymdhi'), 10, function () {
                return \Ixudra\Curl\Facades\Curl::to("https://api.exchangerate.host/latest?base=USD&symbols=TRY")
                    ->asJsonResponse(true)
                    ->get();
            });
            if (isset($response['rates']['TRY'])) {
                return floatval($response['rates']['TRY']);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('priceFormat')) {
    function priceFormat($number, $type = "decimal", $fraction = 8): float|string
    {
        if ($type == "float") {
            return floatval(number_format(floatval($number), $fraction, '.', ''));
        }
        return number_format($number, $fraction, '.', ',');
    }
}


if (!function_exists('parityExchanges')) {
    function parityExchanges($set = [], $changes = [], $convert = false): array
    {
        $list = [
            "price" => 0.0,
            "volume_last_24_hours_price" => 0.0,
            "percent_last_1_hours" => 0.0,
            "percent_last_24_hours" => 0.0,
            "market_price" => 0.0,
            "lowest" => 0.0,
            "highest" => 0.0
        ];
        foreach ($set as $key => $s) {
            if (array_key_exists($key, $changes)) {
                $key = $changes[$key];
            }
            if (array_key_exists($key, $list)) {
                $list[$key] = $s;
            }
        }
        if ($convert) {
            foreach ($list as $lkey => $l) {
                if (strstr($lkey, "price") && $usdToTry = usdToTry()) {
                    $list[$lkey] = $l * $usdToTry;
                }
            }
        }
        return $list;
    }
}

if (!function_exists('mb_strtotitle_tr')) {
    function mb_strtotitle_tr($str): array|bool|string|null
    {
        return mb_convert_case(str_replace(["I", "i"], ["ı", "İ"], $str), MB_CASE_TITLE_SIMPLE, "UTF-8");
    }
}

if (!function_exists('mb_strtoupper_tr')) {
    function mb_strtoupper_tr($str): array|bool|string|null
    {
        return mb_convert_case(str_replace(["i", "I"], ["İ", "ı"], mb_strtolower($str)), MB_CASE_UPPER_SIMPLE, "UTF-8");
    }
}
if (!function_exists('kyc_keys')) {
    #[ArrayShape(['identity_front' => "string", 'identity_back' => "string", 'invoice' => "string", 'selfie' => "string"])] function kyc_keys(): array
    {
        return [
            'identity_front' => 'Kimlik Ön Yüzü',
            'identity_back' => 'Kimlik Arka Yüzü',
            'invoice' => 'Üzerinde Adres, Ad ve Soyad Olan Fatura',
            'selfie' => 'Selfie: Günün tarihi - CoinSwifter.com yazılı kağıt ve Kimlik.'
        ];
    }
}

if (!function_exists('format_float')) {
    function format_float(
        $value
    )
    {
        $phpPrecision = 32;

        if ($value == 0.0) return '0.0';

        if (log10(abs($value)) < $phpPrecision) {

            $decimalDigits = max(
                ($phpPrecision - 1) - floor(log10(abs($value))),
                0
            );

            $formatted = number_format($value, $decimalDigits);

            // Trim excess 0's
            $formatted = preg_replace('/(\.[0-9]+?)0*$/', '$1', $formatted);

            return $formatted;

        }

        $formattedWithoutCommas = number_format($value, 0, '.', '');

        $sign = (strpos($formattedWithoutCommas, '-') === 0) ? '-' : '';

        // Extract the unsigned integer part of the number
        preg_match('/^-?(\d+)(\.\d+)?$/', $formattedWithoutCommas, $components);
        $integerPart = $components[1];

        // Split into significant and insignificant digits
        $significantDigits = substr($integerPart, 0, $phpPrecision);
        $insignificantDigits = substr($integerPart, $phpPrecision);

        // Round the significant digits (using the insignificant digits)
        $fractionForRounding = (float)('0.' . $insignificantDigits);
        $rounding = (int)round($fractionForRounding);  // Either 0 or 1
        $rounded = $significantDigits + $rounding;

        // Pad on the right with zeros
        $formattingString = '%0-' . strlen($integerPart) . 's';
        $formatted = sprintf($formattingString, $rounded);

        // Insert a comma between every group of thousands
        $formattedWithCommas = strrev(
            rtrim(
                chunk_split(
                    strrev($formatted), 3, ','
                ),
                ','
            )
        );

        return $sign . $formattedWithCommas;
    }
}

if (!function_exists('decimal_sum')) {
    function decimal_sum(array $arr = []): string
    {
        $total = "0";
        foreach ($arr as $a) {
            $total = \Litipk\BigNumbers\Decimal::fromString($a)->add(\Litipk\BigNumbers\Decimal::fromString($total), null)->innerValue();
        }
        return $total;
    }
}

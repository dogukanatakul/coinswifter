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




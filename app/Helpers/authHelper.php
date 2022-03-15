<?php

use JetBrains\PhpStorm\ArrayShape;

if (!function_exists('pssMngr')) {
    function pssMngr($value)
    {
        for ($i = 0; $i <= 16; $i++) {
            $value = hash('sha256', hash('sha256', $value) . $value . hash('sha256', $value));
        }
        return $value;
    }
}

if (!function_exists('random_strings')) {
    function getRefererCode($length_of_string = 10): string
    {
        for ($i = 0; $i <= 10; $i++) {
            $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
            $code = substr(str_shuffle($str_result), 0, $length_of_string);
            if (empty(\App\Models\User::where('referance_code', $code)->first())) {
                return $code;
            }
        }
        return $code;
    }
}

if (!function_exists('hiddenImage')) {
    function hiddenImage($name, $ext, $disk, $key = false): bool|string
    {
        try {
            $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
            \Illuminate\Support\Facades\Cache::add($uuid, [
                'file' => \Illuminate\Support\Facades\Storage::disk($disk)->get(($key ? $key . "/" : '') . $name),
                'ext' => $ext
            ], 1500);
            return route('api.get_media', ['uuid' => $uuid]);
        } catch (\Exception $e) {
            return false;
        }
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

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
if(!function_exists('encData')){
    function encData($value){
        $ciphering = "AES-256-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = env('ENCRYPTION_IV');
        $encryption_key = env('ENCRYPTION_KEY');
        $encryption = openssl_encrypt($value, $ciphering, $encryption_key, $options, $encryption_iv);
        return $encryption;
    }
}
if(!function_exists('dcdData')){
    function dcdData($value){
        $ciphering = "AES-256-CTR";
        $options = 0;
        $decryption_iv = env('ENCRYPTION_IV');
        $decryption_key = env('ENCRYPTION_KEY');
        $decryption=openssl_decrypt ($value, $ciphering, $decryption_key, $options, $decryption_iv);
        return $decryption;
    }
}
if (!function_exists('encWllt')){
    function encWllt($value)
    {
        $passphrase = env('PASSPHRASE');
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 128) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
        return json_encode($data);
    }
}
if (!function_exists('dcdWllt')){
    function dcdWllt($value){
        $passphrase = env('PASSPHRASE');
        $jsondata = json_decode(json_encode($value), true);
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv = hex2bin($jsondata["iv"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 7; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data);
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

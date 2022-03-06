<?php


if (!function_exists('nodeIPS')) {
    function nodeIPS($node)
    {
        $list = [
            "dxc" => "http://185.86.81.20:2020",
            "dxc_rest" => [
                "is_wallet" => "/isWallet/{address}", // Cüzdan var mı?
                "balance_wallet" => "/balanceWallet/{address}", // Coinin bakiyesini sorgula
                "balance_token" => "/balanceToken/{address}&{contract}", // Tokenlerin bakiyesini sorgula
                "create_wallet" => "/createWallet/{password}", // cüzdan oluştur
                "send" => "/sendTransaction/{sender}&{password}&{receiver}&{amount}&{fee}&{contract}&{description}",
                "get_fee" => "/getFee/{amount}&{contract}",
                "get_transaction_by_dex_hash" => "/getTransactionByDexHash/{dexhash}",
            ],
            "bsc" => "http://127.0.0.1:2525//BSC_TESTNET",
            "eth" => "http://127.0.0.1:2525//ETH_TESTNET",
        ];
        return $list[$node];
    }
}


if (!function_exists('dxcActions')) {
    function dxcActions($action, $data = []): object|bool
    {
        $actionGet = nodeIPS("dxc_rest")[$action];
        $pattern = '/(?<=\{)(.*?)(?=\})/';
        preg_match_all($pattern, $actionGet, $required);
        if (count(array_diff($required[0], array_keys($data))) > 0) {
            return false;
        }
        $engine = new \StringTemplate\Engine();
        $engine = $engine->render(nodeIPS("dxc_rest")[$action], $data);
        $response = \Ixudra\Curl\Facades\Curl::to(nodeIPS("dxc") . $engine)
            ->withTimeout(5)
            ->withConnectTimeout(5)
            ->asJsonResponse()
            ->returnResponseObject()
            ->get();
        if ($response->status === 200 & isset($response->content->result) && ($response->content->result == "0000" || $response->content->result == "0015")) {
            return (object)[
                'status' => true,
                'content' => $response->content
            ];
        } else if ($response->status === 0 && strstr($response->error, "timed out")) {
            return (object)[
                'status' => false,
                'content' => null
            ];
        } else {
            return (object)[
                'status' => false,
                'content' => $response->content
            ];
        }
    }
}

if (!function_exists('bscActions')) {
    function bscActions($action, $data = false): object
    {
        $response = \Ixudra\Curl\Facades\Curl::to(nodeIPS("bsc") . "/" . $action)
            ->withTimeout(15)
            ->withConnectTimeout(15);
        if ($data) {
            $response = $response->withData($data);
        }
        $response = $response->asJson()
            ->asJsonResponse()
            ->returnResponseObject()
            ->post();
        if ($response->status === 200 & isset($response->content->status) && $response->content->status == "success") {
            return (object)[
                'status' => true,
                'content' => $response->content
            ];
        } else {
            return (object)[
                'status' => false,
                'content' => $response->content
            ];
        }
    }
}

if (!function_exists('ethActions')) {
    function ethActions($action, $data = false): object
    {
        $response = \Ixudra\Curl\Facades\Curl::to(nodeIPS("eth") . "/" . $action)
            ->withTimeout(15)
            ->withConnectTimeout(15);
        if ($data) {
            $response = $response->withData($data);
        }
        $response = $response->asJson()
            ->asJsonResponse()
            ->returnResponseObject()
            ->post();
        if ($response->status === 200 & isset($response->content->status) && $response->content->status == "success") {
            return (object)[
                'status' => true,
                'content' => $response->content
            ];
        } else {
            return (object)[
                'status' => false,
                'content' => $response->content
            ];
        }
    }
}


if (!function_exists('bossWallets')) {
    function bossWallets($node): object
    {
        $list = [
            "dxc" => [
                'address' => 'CX2559f55c79cb427f82695edbfbdbbad0',
                'password' => '63333838396438632d666435632d343037352d623666632d343563323434636234643334',
                'password_source' => 'c3889d8c-fd5c-4075-b6fc-45c244cb4d34',
            ],
            "bsc" => [
                "address" => "0x914c69916f0621b1350edbdb86c634b54955E44d",
                "password" => "0x22706fcc4f3c1bb5b0fd7cd12f99a8b8c301f527619dfeb5a804c0a209a31711"
            ],
            "eth" => [
                "address" => "0x914c69916f0621b1350edbdb86c634b54955E44d",
                "password" => "0x22706fcc4f3c1bb5b0fd7cd12f99a8b8c301f527619dfeb5a804c0a209a31711"
            ]
        ];
        return (object)$list[$node];
    }
}

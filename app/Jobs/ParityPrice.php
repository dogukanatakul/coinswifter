<?php

namespace App\Jobs;

use App\Models\Parity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ParityPrice implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $timeout = 300;
    public int $uniqueFor = 10;

    public array $apisConf = [];
    public array $changeKey = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apisConf = [
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/bitcoin/metrics',
                'symbol' => 'BTC',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/litecoin/metrics',
                'symbol' => 'LTC',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/eth/metrics',
                'symbol' => 'ETH',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/dogecoin/metrics',
                'symbol' => 'DOGE',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/qtum/metrics',
                'symbol' => 'QTUM',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/xrp/metrics',
                'symbol' => 'XRP',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/holochain/metrics',
                'symbol' => 'HOT',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/binance-coin/metrics',
                'symbol' => 'BNB',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/avalanche/metrics',
                'symbol' => 'AVAX',
            ],
            [
                'type' => 'messari',
                'url' => 'https://data.messari.io/api/v1/assets/solana/metrics',
                'symbol' => 'SOL',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=PIT&amount=1',
                'symbol' => 'PIT',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=CATE&amount=1',
                'symbol' => 'CATE',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=HAM&amount=1',
                'symbol' => 'HAM',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=FLOKI&amount=1',
                'symbol' => 'FLOKI',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=SXP&amount=1',
                'symbol' => 'SXP',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=BABYDOGE&amount=1',
                'symbol' => 'BABYDOGE',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=FTM&amount=1',
                'symbol' => 'FTM',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=TWT&amount=1',
                'symbol' => 'TWT',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=ZIG&amount=1',
                'symbol' => 'ZIG',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=CHZ&amount=1',
                'symbol' => 'CHZ',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=GRT&amount=1',
                'symbol' => 'GRT',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=SHIB&amount=1',
                'symbol' => 'SHIB',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=ENJ&amount=1',
                'symbol' => 'ENJ',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=HOT&amount=1',
                'symbol' => 'HOT',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=IOTX&amount=1',
                'symbol' => 'IOTX',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=ELON&amount=1',
                'symbol' => 'ELON',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=SAND&amount=1',
                'symbol' => 'SAND',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=RACA&amount=1',
                'symbol' => 'RACA',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=CEEK&amount=1',
                'symbol' => 'CEEK',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=ALICE&amount=1',
                'symbol' => 'ALICE',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=BUSD&amount=1',
                'symbol' => 'BUSD',
            ],
            [
                'type' => 'coinmarketcap',
                'url' => 'https://web-api.coinmarketcap.com/v1/tools/price-conversion?symbol=SHIP&amount=1',
                'symbol' => 'SHIP',
            ],
        ];

        $this->changeKey = [
            "price_usd" => "price",
            "volume_last_24_hours" => "volume_last_24_hours_price",
            "percent_change_usd_last_1_hour" => "percent_last_1_hours",
            "percent_change_usd_last_24_hours" => "percent_last_24_hours",
            "current_marketcap_usd" => "market_price",
        ];
    }


    public static function keepMonitorOnSuccess(): bool
    {
        return false;
    }


    /**
     * Execute the job.
     *
     * @return array
     * @throws \Exception
     */


    public function parseMessari($conf): array
    {
        $childList = [];
        $response = Curl::to($conf['url'])
            ->asJsonResponse(true)
            ->get();
        if (isset($response['data']['market_data'])) {
            foreach ($response['data']['market_data'] ?? [] as $market_key => $market) {
                if (is_array($market)) {
                    foreach ($market as $child_key => $child) {
                        $childList[$market_key . "_" . $child_key] = $child;
                    }
                } else {
                    $childList[$market_key] = $market;
                }
            }
            foreach ($response['data']['marketcap'] ?? [] as $cap_key => $cap) {
                if (is_array($cap)) {
                    foreach ($cap as $child_key => $child) {
                        $childList[$cap_key . "_" . $child_key] = $child;
                    }
                } else {
                    $childList[$cap_key] = $cap;
                }
            }
        }
        return $childList;
    }

    public function coinmarketcapParse($conf): array
    {
        $childList = [];
        $response = Curl::to($conf['url'])
            ->withHeaders([
                'X-CMC_PRO_API_KEY: 66402d68-3fa4-4ee7-aadb-287fcba38abc',
                'accepts: application/json',
                'Host: web-api.coinmarketcap.com',
                'user-agent: insomnia/2021.7.2',
            ])
            ->asJsonResponse(true)
            ->get();
        if (isset($response['data']['quote']['USD']['price'])) {
            $childList['price'] = $response['data']['quote']['USD']['price'];
        }
        return $childList;
    }


    /**
     * @throws \Exception
     */
    public function getParityChanges(): array
    {
        $this->queueProgress(0);
        try {
            $list = [];
            $parities = Parity::with(['source' => function ($query) {
                $query->whereIn('symbol', ['USDT', 'TRY']);
            }, 'coin' => function ($q) {
                $q->with(['network' => function ($q) {
                    $q->whereIn('short_name', ['BSC', 'ETH']);
                }]);
            }])
                ->has('source')
                ->get()
                ->makeVisible('id')
                ->groupBy('coin.symbol');
            foreach ($this->apisConf as $conf) {
                foreach ($parities[$conf['symbol']] ?? [] as $parity) {
                    if ($conf['type'] === 'messari') {
                        $childList = $this->parseMessari($conf);
                    } else if ($conf['type'] === 'coinmarketcap') {
                        $childList = $this->coinmarketcapParse($conf);
                    }
                    if (isset($childList) && count($childList) > 0) {
                        $list[$parity->id] = parityExchanges($childList, $this->changeKey, $parity->source->sembol == "TRY");
                    }
                }
            }
            $this->queueProgress(50);
            $this->queueData(['status' => 'success', 'message' => '']);
            return $list;
        } catch (\Exception $e) {
            printf("getParityChanges ---" . $e->getMessage() . "\n\r");
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }


    /**
     * @throws \Exception
     */
    public function setParityChanges(): bool
    {
        try {
            $parities = $this->getParityChanges();
            foreach ($parities as $id => $params) {
                \App\Models\ParityPrice::where('parities_id', $id)->where('source', 'outsource')->delete();
                foreach (array_filter($params, function ($value) {
                    return !is_null($value) && $value !== '';
                }) as $param_key => $param) {
                    \App\Models\ParityPrice::create([
                        'parities_id' => $id,
                        'value' => $param,
                        'source' => "outsource",
                        'type' => $param_key
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            printf("setParityChanges ---" . $e->getMessage() . "\n\r");
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }


    /**
     * @throws \Exception
     */
    public function handle(): bool
    {
        $this->setParityChanges();
        return true;
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}

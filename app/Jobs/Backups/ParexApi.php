<?php

namespace App\Jobs\Backups;

use App\Models\Parity;
use App\Models\ParityPrice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use function collect;
use function parityExchanges;
use function storage_path;

class ParexApi implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $timeout = 300;
    public int $uniqueFor = 10;
    public int $tries = 1;
    public array $changeKey = [];


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->changeKey = [
            "PRICE" => "price",
            "VOLUMESORTING" => "volume_last_24_hours_price",
            "CHANGESORTING" => "percent_last_24_hours",
        ];
    }


    public static function keepMonitorOnSuccess(): bool
    {
        return false;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */


    public function connectionParex()
    {
        try {
            $proxy = [
                'ip' => '52.183.8.192',
                'port' => 3128,
                'type' => 'https://',
            ];
            $userId = 112;

            $this->queueProgress(10);
            Curl::to('https://api.parex.exchange/controlLogin.php')
                ->withProxy($proxy['ip'], $proxy['port'], $proxy['type'])
                ->withTimeout(15)
                ->withConnectTimeout(15)
                ->withHeader("Accept: application/json")
                ->withHeader("Host: api.parex.exchange")
                ->withContentType('application/x-www-form-urlencoded')
                ->withResponseHeaders()
                ->returnResponseObject()
                ->setCookieFile(storage_path('app/public/cookie.txt'))
                ->setCookieJar(storage_path('app/public/cookie.txt'))
                ->withData([
                    'apikey' => '8d0a19ce960546fba4e4639b5a98321abf32a3e60da04d809470df1a13736a01',
                    'parexID' => $userId,
                    'parexKey' => 'b4wh4M5moP3LUInjZWIRMAb4wh4M5moP3LUInjZWIRMA', // d77ed3ea4eab11ecad5402ca075254e8UWaNIpxPeu
                ])
                ->post();
            $this->queueProgress(35);
            Curl::to('https://api.parex.exchange/setActiveUserv3.php')
                ->withProxy($proxy['ip'], $proxy['port'], $proxy['type'])
                ->withTimeout(15)
                ->withConnectTimeout(15)
                ->withHeader("Accept: application/json")
                ->withHeader("Host: api.parex.exchange")
                ->withHeader("Cookie: PHPSESSID=0sjggb0a9pdo0ds985bvncfhvp")
                ->withContentType('application/x-www-form-urlencoded')
                ->withResponseHeaders()
                ->returnResponseObject()
                ->setCookieFile(storage_path('app/public/cookie.txt'))
                ->setCookieJar(storage_path('app/public/cookie.txt'))
                ->withData([
                    'apikey' => 'b3373e847c4444408e39dd81e6db3a0f93f8c66aaf3c4968b5ab805858d03935',
                    'prxid' => $userId,
                    'secret' => hash("sha256", "RestPrx" . ($userId * 2.0 - 1.0) . "Secret"),
                    'deviceType' => 'Physical',
                    'idiom' => 'Phone',
                    'platform' => 'Android',
                    'version' => '10',
                    'deviceName' => 'LG G4',
                    'manufacturer' => 'LGE',
                    'device' => 'LG-H815',
                    'prxVersion' => '1.0.21',
                    'playerid' => 'adb5bdf8-0b2c-4f72-9f66-1140384bf6c8',
                ])
                ->post();

            $this->queueProgress(50);
            $response = Curl::to('https://api.parex.exchange/getMarketInfov2.php')
                ->withProxy($proxy['ip'], $proxy['port'], $proxy['type'])
                ->withTimeout(15)
                ->withConnectTimeout(15)
                ->withHeader("Accept: application/json")
                ->withHeader("Host: api.parex.exchange")
                ->withContentType('application/x-www-form-urlencoded')
                ->withResponseHeaders()
                ->returnResponseObject()
                ->setCookieFile(storage_path('app/public/cookie.txt'))
                ->setCookieJar(storage_path('app/public/cookie.txt'))
                ->withData([
                    "apikey" => "56f92132a51f4e6db289a5d5d5441a7d62b01b20611c4e0dac1aa868fca3d877",
                    "prxid" => $userId,
                    "secret" => hash("sha256", "RestPrx" . ($userId * 2.0 - 1.0) . "Secret"),
                    "secureCode" => hash("sha256", "RestPrx" . ($userId * 2.0 - 1.0) . "Markets")
                ])
                ->asJsonResponse(true)
                ->post();
            $this->queueProgress(70);
            $data = collect($response->content['data'])->mapWithKeys(function ($data) {
                return [$data['PAIRINFO'] => $data];
            })->toArray();
            $list = collect($data)->filter(function ($value, $key) {
                return in_array($key, ['PRX', 'DXC', 'BRS']);
            })->toArray();
            return $list;
        } catch (\Exception $e) {
            printf("parex_api ---" . $e->getMessage() . "\n\r");
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }


    public function parseParex()
    {
        $this->queueProgress(80);
        $parexList = $this->connectionParex();
        $parities = Parity::with(['source' => function ($query) {
            $query->whereIn('sembol', ['USDT', 'TRY']);
        }, 'coin'])
            ->whereHas('coin', function ($query) {
                $query->whereIn('sembol', ['PRX', 'DXC', 'BRS']);
            })
            ->has('source')
            ->get()
            ->makeVisible('id')
            ->groupBy('coin.sembol');
        $list = [];
        foreach ($parexList as $prxKey => $prx) {
            foreach ($parities[$prxKey] ?? [] as $parity) {
                $list[$parity->id] = parityExchanges($prx, $this->changeKey, ($parity->source->sembol == "TRY" ? true : false));
            }
        }
        return $list;
    }


    public function handle()
    {
        $parities = $this->parseParex();
        foreach ($parities as $id => $params) {
            ParityPrice::where('parite_ciftleri_id', $id)->where('kaynak', 'parex')->delete();
            foreach ($params as $param_key => $param) {
                ParityPrice::create([
                    'parite_ciftleri_id' => $id,
                    'sayisal' => $param,
                    'kaynak' => "parex",
                    'tipi' => $param_key
                ]);
            }
        }
        $this->queueProgress(100);
        return true;

    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}

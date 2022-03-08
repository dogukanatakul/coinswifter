<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UpgradeProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Upgrade';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (env('APP_ENV') === 'production') {
            $returnVar = NULL;
            $output = NULL;
            exec("service supervisord stop", $output, $returnVar);
        }
        Artisan::call('queue:flush');
        Artisan::call('queue:restart');
        Artisan::call('queue:clear', [
            '--force' => true,
        ]);
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();
        \App\Jobs\ParityPrice::dispatch();
        \App\Jobs\CurrentPrices::dispatch();
        \App\Jobs\NodeTransaction::dispatch();
        \App\Jobs\TransferBSC::dispatch();
        \App\Jobs\TransferETH::dispatch();
        \App\Jobs\TransferDB::dispatch();
        \App\Jobs\Exchange::dispatch();
//        \App\Jobs\CheckBanks::dispatch();
        if (env('APP_ENV') === 'production') {
            exec("service supervisord start", $output, $returnVar);
            printf("Proje kaynakları yenilendi.\n\r");
        }
        return Command::SUCCESS;
    }
}

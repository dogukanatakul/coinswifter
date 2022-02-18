<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database Backup';

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
    public function handle()
    {

        //  > dbname.sql
        $fileName = "backup-" . now()->tz('Europe/Istanbul')->toDateTimeLocalString() . ".gz";
        $filePath = storage_path("app/backups/" . $fileName);
        $ftp = Storage::disk('ftp');

        // parite_degisimleri
        $execs = [];
        $execs[] = "--ignore-table=c01nsw1ft3r.queue_monitor";
        $execs[] = "--ignore-table=c01nsw1ft3r.jobs";
        $execs[] = "--ignore-table=c01nsw1ft3r.failed_jobs";
        $execs[] = "--ignore-table=c01nsw1ft3r.sessions";
        $execs[] = "--ignore-table=c01nsw1ft3r.log_activity";
        $execs[] = "--ignore-table=c01nsw1ft3r.parity_prices";

        $command = "mysqldump -u root --password=F1yazilim2529% c01nsw1ft3r " . implode(" ", $execs) . " | gzip > " . $filePath;
        $returnVar = NULL;
        $output = NULL;
        exec($command, $output, $returnVar);
        if (!$ftp->exists($fileName)) {
            $file = Storage::disk('local')->get("backups/" . $fileName);
            $ftp->put($fileName, $file);
        }
        return Command::SUCCESS;
    }
}

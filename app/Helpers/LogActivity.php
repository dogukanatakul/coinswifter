<?php

namespace App\Helpers;

class LogActivity

{


    public static function addToLog($set = [])
    {
        $log = [];
        $log['subject'] = $set['subject'] ?? null;

        $log['status'] = $set['status'] ?? 'success';
        if ((request()->path() === 'api/signin' || request()->path() === 'api/signup') && $log['status'] === 'success') {
            $log['status'] = null;
        }
        $log['status_text'] = $log['status'] === 'success' ? $set['status_text'] ?? null : mb_strimwidth($set['status_text'] ?? null, 0, 999, "...");

        $log['url'] = request()->fullUrl();
        $log['path'] = request()->path();
        $log['method'] = request()->method();
        $log['ip'] = request()->ip();
        $log['agent'] = request()->header('user-agent');
        $log['users_id'] = $set['user'] ?? null;
        try {
            \App\Models\LogActivity::create($log);
        } catch (\Exception $exception) {
            if (PHP_SAPI === 'cli') {
                printf("LogActivity Atlandı!\n\r");
            }
        }
    }


    public static function logActivityLists()
    {
        return \App\Models\LogActivity::latest()->get();

    }


}

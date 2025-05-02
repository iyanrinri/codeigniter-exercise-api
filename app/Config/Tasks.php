<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseConfig
{
    public function init(Scheduler $schedule)
    {
        // Tasks are managed via system cron
        $schedule->command('auth:cleanup-tokens')->everyFifteenMinutes();
    }
}

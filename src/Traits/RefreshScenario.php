<?php

namespace Clevyr\LaravelBehatDusk\Traits;

use Illuminate\Support\Facades\Artisan;

trait RefreshScenario
{
    /**
     * @BeforeScenario
     */
    public static function freshDatabase()
    {
        Artisan::call('migrate:fresh --seed');
    }
}

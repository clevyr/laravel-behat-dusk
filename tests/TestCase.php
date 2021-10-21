<?php

namespace Clevyr\LaravelBehatDusk\Tests;

use Clevyr\LaravelBehatDusk\LaravelBehatDuskServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelBehatDuskServiceProvider::class,
            DuskServiceProvider::class,
        ];
    }
}

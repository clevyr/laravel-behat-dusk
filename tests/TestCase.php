<?php

namespace Clevyr\LaravelBehatDusk\Tests;

use Clevyr\LaravelBehatDusk\LaravelBehatDuskServiceProvider;

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
        ];
    }
}

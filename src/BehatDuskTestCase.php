<?php

namespace Clevyr\LaravelBehatDusk;

use Behat\Behat\Context\Context;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

abstract class BehatDuskTestCase extends DuskTestCase implements Context
{
    /**
     * BehatDuskTestCase constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUp();

        static::startChromeDriver();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $base_url = config('behat-dusk.dusk_base_url');

        if (is_null($base_url)) {
            throw new \Exception('Behat base url is not set.');
        }

        Browser::$baseUrl = rtrim($base_url, '/');
    }
}

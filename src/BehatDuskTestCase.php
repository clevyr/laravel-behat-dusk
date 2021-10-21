<?php

namespace Clevyr\LaravelBehatDusk;

use Behat\Behat\Context\Context;
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
    }
}

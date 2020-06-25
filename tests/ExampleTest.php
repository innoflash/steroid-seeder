<?php

namespace Innoflash\EnvUpdater\Tests;

use Orchestra\Testbench\TestCase;
use Innoflash\EnvUpdater\SteroidSeederServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [SteroidSeederServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}

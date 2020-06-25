<?php

namespace Innoflash\SteroidSeeder\Tests;

use Innoflash\SteroidSeeder\SteroidSeederServiceProvider;
use Orchestra\Testbench\TestCase;

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

<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Innoflash\SteroidSeeder\FactoryBuilder factory($class, $amount = null)
 */
class SteroidSeeder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'steroid-seeder';
    }
}

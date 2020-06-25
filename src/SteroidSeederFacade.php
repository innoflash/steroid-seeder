<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Innoflash\SteroidSeeder\Skeleton\SkeletonClass
 */
class SteroidSeederFacade extends Facade
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

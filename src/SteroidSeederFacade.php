<?php

namespace Innoflash\EnvUpdater;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Innoflash\EnvUpdater\Skeleton\SkeletonClass
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

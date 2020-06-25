<?php

namespace Innoflash\SteroidSeeder;

class SteroidSeederManager
{
    /**
     * Create a model factory builder for a given class and amount.
     *
     * @param $class
     * @param  null  $amount
     *
     * @return \Innoflash\SteroidSeeder\FactoryBuilder
     */
    public function factory($class, $amount = null)
    {
        return steroidFactory($class, $amount);
    }
}

<?php

use Innoflash\SteroidSeeder\Factory;

if (! function_exists('steroidFactory')) {
    /**
     * Create a model factory builder for a given class and amount.
     *
     * @param  string  $class
     * @param  int  $amount
     *
     * @return \Innoflash\SteroidSeeder\FactoryBuilder
     */
    function steroidFactory($class, $amount = null)
    {
        $factory = app(Factory::class);

        if (isset($amount) && is_int($amount)) {
            return $factory->of($class)->times($amount);
        }

        return $factory->of($class);
    }
}

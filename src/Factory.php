<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Database\Eloquent\Factory as LaravelFactory;

class Factory extends LaravelFactory
{
    /**
     * Create a builder for the given model.
     *
     * @param  string  $class
     *
     * @return \Innoflash\SteroidSeeder\FactoryBuilder
     */
    public function of($class)
    {
        return new FactoryBuilder(
            $class, $this->definitions, $this->states,
            $this->afterMaking, $this->afterCreating, $this->faker
        );
    }
}

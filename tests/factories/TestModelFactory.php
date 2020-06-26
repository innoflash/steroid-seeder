<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Innoflash\SteroidSeeder\Tests\Models\TestModel;

$factory->define(TestModel::class, function (Faker $faker) {
    return [
        'name'    => $faker->name,
        'email'   => $faker->email,
        'address' => $faker->address,
    ];
});

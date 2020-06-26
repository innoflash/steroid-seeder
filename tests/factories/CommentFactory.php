<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Innoflash\SteroidSeeder\Tests\Models\Comment;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'comment' => $faker->sentence(10),
        'user_name' => $faker->userName,
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Tweet;
use Faker\Generator as Faker;

$factory->define(Tweet::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return \App\User::query()->inRandomOrder()->first()->id;
        },
        'content' => $faker->word
    ];
});

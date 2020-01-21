<?php

use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'owner_id' => factory(App\User::class),
        'title' => $faker->sentence(4),
        'description' => $faker->sentence(4),
        'notes' => $faker->sentence(4),
    ];
});

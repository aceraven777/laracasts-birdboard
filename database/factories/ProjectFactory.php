<?php

use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'owner_id' => factory(App\User::class)->create()->id,
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
    ];
});

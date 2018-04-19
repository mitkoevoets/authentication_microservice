<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Entities\ActivationToken::class, function (Faker $faker) {
    return [
        'token' => base64_encode(str_random(100)),
        'user_id' => factory(\App\Entities\User::class)->create()->id
    ];
});

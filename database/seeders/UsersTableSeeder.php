<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     * @return void
     */
    public function run() {
        $blueprint = empty(func_get_args()) ? config('blueprints.default') : func_get_args()[0];

        foreach (Arr::get($blueprint, 'users', []) as $item) {
            User::factory()->create($item);
        }

    }
}

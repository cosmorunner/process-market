<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TagFactory
 * @package Database\Factories
 */
class TagFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'name' => $this->faker->word,
            'color' => $this->faker->randomElement(config('colors'))
        ];
    }

    /**
     * Name des Tags setzen.
     * @param string $name
     * @return TagFactory
     */
    public function name(string $name) {
        return $this->state(function () use ($name) {
            return [
                'name' => $name
            ];
        });
    }

    /**
     * Farbe des Tags setzen.
     * @param string $color
     * @return TagFactory
     */
    public function color(string $color) {
        return $this->state(function () use ($color) {
            return [
                'color' => $color
            ];
        });
    }

    /**
     * Fügt einen Prozess zum Graph hinzu.
     * @return TagFactory
     */
    public function withProcess() {
        return $this->afterCreating(function (Tag $tag) {
            $process = Process::factory()->create();
            $tag->processes()->save($process);
        });
    }
}

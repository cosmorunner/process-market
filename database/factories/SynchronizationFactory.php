<?php

namespace Database\Factories;

use App\Models\ProcessVersion;
use App\Models\Synchronization;
use App\Models\System;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 * Class SynchronizationFactory
 * @package Database\Factories
 */
class SynchronizationFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Synchronization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'system_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'subject_id' => $this->faker->uuid,
            'subject_type' => ProcessVersion::class,
            'response_code' => Response::HTTP_OK,
            'response_message' => 'Ok.',
        ];
    }

    /**
     * Markiert die Synchronisierung als ein Erfolg.
     * @return SynchronizationFactory
     */
    public function success() {
        return $this->state(function () {
            return [
                'response_code' => Response::HTTP_OK,
                'response_message' => 'Ok.'
            ];
        });
    }

    /**
     * Markiert die Synchronisierung als ein Fehler.
     * @return SynchronizationFactory
     */
    public function failure() {
        return $this->state(function () {
            return [
                'response_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'response_message' => 'Server-Error.'
            ];
        });
    }

    /**
     * Setzt das System, zu dem eine Synchronisation ausgeführt wurde.
     * @param System $system
     * @return SynchronizationFactory
     */
    public function toSystem(System $system) {
        return $this->state(function () use ($system) {
            return [
                'system_id' => $system->id
            ];
        });
    }

    /**
     * Setzt das Subject, von dem eine Synchronisation ausgeführt wurde.
     * @param Model|ProcessVersion $subject
     * @return SynchronizationFactory
     */
    public function ofSubject(Model $subject) {
        return $this->state(function () use ($subject) {
            return [
                'subject_id' => $subject->id,
                'subject_type' => get_class($subject),
            ];
        });
    }


    /**
     * Benutzer, der die Synchronisierung durchgeführt hat.
     * @param User $user
     * @return SynchronizationFactory
     */
    public function byUser(User $user) {
        return $this->state(function () use ($user) {
            return [
                'user_id' => $user->id
            ];
        });
    }
}

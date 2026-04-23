<?php

namespace App\Traits;

use App\Models\License;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Trait for models that can be licensed, e.g. a process or a solution.
 */
trait UsesLicenses {

    /**
     * All create licenses where the resource is the entity.
     * @return MorphMany
     */
    public function licenses(): MorphMany {
        return $this->morphMany(License::class, 'resource');
    }

    /**
     * Creates an open source license for the process.
     * @param $options
     * @param User|Organisation $receiver
     * @return License
     */
    public function createLicense($options, User|Organisation $receiver): License {
        /* @var User $user */
        $user = auth()->user();

        return License::create([
            'resource_type' => $this::class,
            'resource_id' => $this->id,
            'owner_type' => $receiver::class,
            'owner_id' => $receiver->id,
            'issuer_id' => $user->id,
            'level' => $options['level'],
            'level_options' => $options['level_options'] ?? []
        ]);
    }

    /**
     * Returns the path to the profile (Licenses tab) of the owner (person or organization).
     * @return string
     * @noinspection PhpUnused
     */
    public function authorProfileLicensesPath(): string {
        if ($this->author instanceof Organisation || $this->author instanceof User) {
            return $this->author->profileLicensesPath();
        }

        return route('index');
    }

    /**
     * Checks whether the requested license matches the license options of the licensable model,
     * by checking the license options as rules with the requested options in a
     * validator.
     * @param array $requested
     * @return bool
     */
    public function validLicenseRequest(array $requested): bool {
        $requestedLevel = $requested['level'] ?? 'bar';
        $exists = collect($this->license_options)->first(fn($item) => $item['level'] === $requestedLevel);

        // Check level
        return is_array($exists);
    }

    /**
     * Returns a collection of all users who have access to the process.
     * Either the user is the author or because a user is part of the organization to which the process belongs
     * or the user has a license for the process.
     * @return Collection
     */
    public function accessibleUsers(): Collection {
        $users = collect();

        if ($this->author instanceof User) {
            $users = $users->add($this->author);
        }

        if ($this->author instanceof Organisation) {
            $users = $users->concat($this->author->members());
        }

        /* @var License $license */
        foreach ($this->licenses()->with('owner') as $license) {
            if ($license->owner instanceof User) {
                $users = $users->add($license->owner);
            }
            if ($license->owner instanceof Organisation) {
                $users = $users->concat($license->owner->members());
            }
        }

        return $users->unique('id');
    }

    /**
     * Flag whether the process has an open source license.
     * @return bool
     * @noinspection PhpUnused
     */
    public function hasOpenSourceLicense(): bool {
        return collect($this->license_options)->reduce(function($carry, $item) {
            return $carry || ($item['level'] ?? null) === License::LEVEL_OPEN_SOURCE;
        }, false);
    }

    /**
     * Flag whether the entity has a license that can be purchased publicly.
     * @return bool
     */
    public function hasPublicLicense(): bool {
        return collect($this->license_options)->reduce(function($carry, $item){
            return $carry || ($item['level'] ?? License::LEVEL_PRIVATE) !== License::LEVEL_PRIVATE;
        }, false);
    }

    /**
     * Flag if entity has no license
     * @return bool
     */
    public function hasNoLicense(): bool {
        return collect($this->license_options)->reduce(function($carry, $item) {
            return $carry || ($item['level'] ?? License::LEVEL_NO_LICENSE) === License::LEVEL_NO_LICENSE;
        }, false);
    }
}

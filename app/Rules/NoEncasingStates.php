<?php

namespace App\Rules;

use App\ProcessType\State;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class NoOverlappingStates
 * @package App\Rules
 */
class NoEncasingStates implements ValidationRule {

    /**
     * @var Collection|State[]
     */
    private Collection|array $states;

    /**
     * @var string|null
     */
    private string|null $min;

    /**
     * Create a new rule instance.
     * @param Collection $states
     * @param string|null $min
     */
    public function __construct(Collection $states, string|null $min) {
        $this->states = $states;
        $this->min = $min;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        try {
            $value = State::validateValue($value);
        }
        catch (Exception) {
            $fail('Der Wertebereich schließt einen anderen Zustand mit ein.');
        }

        // if min is null it will be calculated therefor it cannot be a range
        if ($this->min === null || $this->min === $value) {
            return;
        }

        foreach ($this->states as $state) {
            if ($state->min > $this->min && $state->max < $value) {
                $fail('Der Wertebereich schließt einen anderen Zustand mit ein.');
            }
        }
    }

}

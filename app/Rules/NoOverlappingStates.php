<?php

namespace App\Rules;

use App\ProcessType\State;
use App\ProcessType\StatusType;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class NoOverlappingStates
 * @package App\Rules
 */
class NoOverlappingStates implements ValidationRule {

    /**
     * @var Collection|State[]
     */
    private Collection|array $states;

    /**
     * Create a new rule instance.
     * @param Collection $states
     */
    public function __construct(Collection $states) {
        $this->states = $states;
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
            $fail('Ein anderer Zustand deckt diesen Wertebereich ab.');
        }

        foreach ($this->states as $state) {
            if (in_array(bccomp($value, $state->min, StatusType::VALUE_PRECISION), [
                    0,
                    1
                ]) && in_array(bccomp($value, $state->max, StatusType::VALUE_PRECISION), [0, -1])) {
                $fail('Ein anderer Zustand deckt diesen Wertebereich ab.');
            }
        }
    }

}

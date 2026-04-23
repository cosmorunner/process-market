<?php

namespace App\Rules;

use App\ProcessType\State;
use App\ProcessType\StatusType;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class NoOverlappingStatesBulk
 * @package App\Rules
 */
class NoOverlappingStatesBulk implements ValidationRule {


    /**
     * @var Collection|State[]
     */
    private Collection|array $states;

    private $newStates = [];

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
     * @throws Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        foreach ($value as $row) {
            $parts = collect(explode(';', $row))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);

            // When min value is empty, we dont need to validate, because it means that the command must automatically identify
            // a valid value.
            if (empty($parts[1] ?? '')) {
                continue;
            }

            $minVal = $parts[1];
            $maxVal = $parts[2] ?? $minVal;
            $maxVal = $this->validateValue($maxVal);

            if (!$minVal || !$maxVal) {
                $fail('Ein anderer Zustand deckt diesen Wertebereich ab.');
                return;
            }

            $this->newStates[] = [
                'min' => $minVal,
                'max' => $maxVal
            ];
        }

        foreach ($this->newStates as $outerKey => $outerValue) {
            foreach ($this->newStates as $innerKey => $innerValue) {
                if ($outerKey === $innerKey) {
                    continue;
                }
                if (in_array(bccomp($value, $innerValue['min'], StatusType::VALUE_PRECISION), [
                        0,
                        1
                    ]) && in_array(bccomp($value, $innerValue['max'], StatusType::VALUE_PRECISION), [0, -1])) {
                    $fail('Einer der neuen Zustände deckt diesen Wertebereich bereits ab.');

                    return;
                }
            }
        }
    }

    private function validateValue(string $value): string {
        try {
            $value = State::validateValue($value);
        }
        catch (Exception $ex) {
            return $ex->getMessage();
        }

        foreach ($this->states as $state) {
            if (in_array(bccomp($value, $state->min, StatusType::VALUE_PRECISION), [
                    0,
                    1
                ]) && in_array(bccomp($value, $state->max, StatusType::VALUE_PRECISION), [0, -1])) {
                return '';
            }
        }
        return $value;
    }
}

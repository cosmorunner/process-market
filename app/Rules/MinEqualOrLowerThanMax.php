<?php

namespace App\Rules;

use App\Processtype\State;
use App\Processtype\StatusType;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class MinLowerThanMax
 * @package App\Rules
 */
class MinEqualOrLowerThanMax implements ValidationRule {

    /**
     * Wert mit dem verglichen wird.
     * @var string
     */
    private $max;

    /**
     * Create a new rule instance.
     * @param $max
     */
    public function __construct($max) {
        $this->max = $max;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->max) {
            return;
        }

        try {
            $value = State::validateValue($value);
            $max = State::validateValue($this->max);
        }
        catch (Exception) {
            $fail('Der Min-Wert muss kleiner oder gleich sein wie der Max-Wert' . $this->max);

            return;
        }

        if (!in_array(bccomp($value, $max ?? $value, StatusType::VALUE_PRECISION), [0, -1])) {
            $fail('Der Min-Wert muss kleiner oder gleich sein wie der Max-Wert ' . $this->max);
        }
    }

}

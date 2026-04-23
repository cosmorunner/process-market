<?php

namespace App\Rules;

use App\Processtype\State;
use App\Processtype\StatusType;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class MaxGreaterThanMin
 * @package App\Rules
 */
class MaxEqualOrGreaterThanMin implements ValidationRule {

    /**
     * Wert mit dem verglichen wird.
     * @var string
     */
    private $min;

    /**
     * Create a new rule instance.
     * @param $min
     */
    public function __construct($min) {
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
        if (!$this->min) {
            $fail('Fehlender Min-Wert für einen Wertevergleich.');

            return;
        }

        try {
            $value = State::validateValue($value);
            $min = State::validateValue($this->min);
        }
        catch (Exception) {
            $fail('Der Max-Wert muss größer oder gleich sein wie der Min-Wert ' . $this->min);

            return;
        }

        if (!in_array(bccomp($value, $min, StatusType::VALUE_PRECISION), [0, 1])) {
            $fail('Der Max-Wert muss größer oder gleich sein wie der Min-Wert ' . $this->min);
        }
    }

}

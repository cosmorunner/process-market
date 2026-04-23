<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeleteTask
 * @package App\Rules\Environment
 */
class DeleteTask implements ValidationRule {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * DeleteTask constructor.
     */
    public function __construct() {
        $this->environment = request('environment');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'identifier' => ['required', 'string', Rule::in($this->environment->blueprint->tasks->pluck('identifier'))],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}

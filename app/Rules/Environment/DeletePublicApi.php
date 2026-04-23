<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeletePublicApi
 * @package App\Rules\Environment
 */
class DeletePublicApi implements ValidationRule {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * DeletePublicApi constructor.
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
            'id' => ['required', 'uuid', Rule::in($this->environment->blueprint->publicApis->pluck('id'))],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}

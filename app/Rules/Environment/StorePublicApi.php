<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StorePublicApi
 * @package App\Rules\Environment
 */
class StorePublicApi implements ValidationRule {

    private Environment $environment;

    /**
     * StorePublicApi constructor.
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
        $existingPublicApiIds = $this->environment->blueprint->publicApis->pluck('id');
        $existingPublicApiIdentifiers = $this->environment->blueprint->publicApis->pluck('identifier');

        $validator = Validator::make((array) $value, [
            'id' => ['nullable', 'uuid', Rule::notIn($existingPublicApiIds)],
            'name' => ['required', 'string', 'max:30'],
            'slug' => [
                'required',
                'string',
                'max:30',
                new ValidSlugFormat,
                Rule::notIn($existingPublicApiIdentifiers)
            ],
        ], [], [
            'slug' => 'Slug'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}

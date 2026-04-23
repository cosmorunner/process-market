<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class TagCount
 * @package App\Rules
 */
class TagCount implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $tags = array_unique(array_filter(explode(';', $value), function ($tag) {
            return strlen(trim($tag)) > 0;
        }));

        if (count($tags) > 3) {
            $fail('validation.custom.tags.count')->translate();
        }
    }

}

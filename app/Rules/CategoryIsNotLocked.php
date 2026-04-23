<?php

namespace App\Rules;

use App\ProcessType\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class CategoryIsNotLocked
 * @package App\Rules
 */
class CategoryIsNotLocked implements ValidationRule {

    /**
     * Alle Kategorien des Prozesstyps.
     * @var Collection
     */
    private Collection $categories;

    /**
     * CategoryIsNotLocked constructor.
     * @param Collection $categories
     */
    public function __construct(Collection $categories) {
        $this->categories = $categories;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $categoryToDelete = $this->categories->firstWhere('id', '=', $value);

        if (!($categoryToDelete instanceof Category && !$categoryToDelete->locked)) {
            $fail('System-Kategorien können nicht gelöscht werden.');
        }
    }

}

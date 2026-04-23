<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class UniqueActionTypeReference
 * @package App\Rules
 */
class UniqueActionTypeReference implements ValidationRule {

    private Collection $actionTypes;
    private ?string $exceptedId;

    /**
     * Create a new rule instance.
     * @param Collection $actionTypes
     * @param string|null $exceptedId
     */
    public function __construct(Collection $actionTypes, string $exceptedId = null) {
        $this->actionTypes = $actionTypes;
        $this->exceptedId = $exceptedId;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $id = $this->exceptedId;
        if ($id) {
            $this->actionTypes = $this->actionTypes->reject(function ($actionType) use ($id) {
                return $actionType->id === $id;
            });
        }

        if ($this->actionTypes->pluck('reference')->contains($value)) {
            $fail('Ein Aktion mit diesem Referenz existiert bereits.');
        }
    }

}

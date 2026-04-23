<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class UniqueStatusTypeReference
 * @package App\Rules
 */
class UniqueStatusTypeReference implements ValidationRule {

    private Collection $statusTypes;
    private ?string $exceptedId;

    /**
     * Create a new rule instance.
     * @param Collection $statusTypes
     * @param string|null $exceptedId
     */
    public function __construct(Collection $statusTypes, string $exceptedId = null) {
        $this->statusTypes = $statusTypes;
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
            $this->statusTypes = $this->statusTypes->reject(function ($statusType) use ($id) {
                return $statusType->id === $id;
            });
        }

        if ($this->statusTypes->pluck('reference')->contains($value)) {
            $fail('Ein Status mit diesem Referenz existiert bereits.');
        }
    }

}

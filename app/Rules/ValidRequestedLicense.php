<?php

namespace App\Rules;

use App\Interfaces\Licensable;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ValidRequestedLicense
 * @package App\Rules
 */
class ValidRequestedLicense implements ValidationRule {

    /**
     * Wert mit dem verglichen wird.
     */
    private ?Licensable $licensable;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $modelId = request('resource_id');
        $className = request('resource_type');

        /* @var Model|Builder $model */
        if (class_exists($className) && ($model = new $className()) instanceof Model) {
            $this->licensable = $model::find($modelId);
        }
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->licensable?->validLicenseRequest($value)) {
            $fail('Die Lizenz-Anfrage ist ungültig.');
        }
    }

}

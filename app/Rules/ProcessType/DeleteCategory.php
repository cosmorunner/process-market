<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\Rules\CategoryIsNotLocked;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeleteCategory
 * @package App\Rules\ProcessType
 */
class DeleteCategory implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    /**
     * @var Definition
     */
    private Definition $definition;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $categories = $this->definition->categories;
        $validator = Validator::make((array) $value, [
            'id' => [
                'bail',
                'required',
                'uuid',
                Rule::in($categories->pluck('id')),
                new CategoryIsNotLocked($categories)
            ],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}

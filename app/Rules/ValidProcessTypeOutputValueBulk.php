<?php

namespace App\Rules;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidProcessTypeOutputValueBulk
 * @package App\Rules
 */
class ValidProcessTypeOutputValueBulk implements ValidationRule {

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
        $parts = array_map('trim', explode(';', $value));
        $name = $parts[0];

        if (isset($parts[2])) {
            $fail('Es wurde mehr Attribute für eine Zeile angegeben, als erwartet. Maximal erwartet: 2.');
            return;
        }

        if (str_starts_with($name, '=')){
            $name = substr($name, 1);
        }
        else if (str_starts_with($name, '~')){
            $name = substr($name, 1);
        }

        $value = [
            'name' => $name,
            'description' => empty($parts[1]) ? '' : $parts[1]
        ];

        $validator = Validator::make($value, [
            'name' => ['required', 'string', 'max:60', new UniqueOutput($this->definition->outputs), new ValidInputOutputName],
            'description' => ['nullable', 'string', 'max:200']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}
<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\Rules\UniqueStatusTypeReference;
use App\Rules\ValidReference;
use App\Rules\ValidStatusValue;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateStatusType
 * @package App\Rules\ProcessType
 */
class UpdateStatusType implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;
    /**
     * @var Definition
     */
    private Definition $definition;

    /**
     * @var array
     */
    private $statusTypeIds;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->statusTypeIds = $this->definition->statusTypes->pluck('id');
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
            'id' => ['required', 'uuid', Rule::in($this->statusTypeIds)],
            'reference' => [
                'required',
                'string',
                'max:40',
                new ValidReference,
                new UniqueStatusTypeReference($this->definition->statusTypes, $value['id'])
            ],
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:200'],
            'default' => ['required', 'string', new ValidStatusValue],
            'hidden' => ['required', 'bool'],
            'image' => ['nullable', 'string'],
            'components' => ['array'],
            'sort' => ['required', 'integer'],
            'namespace' => ['required', 'string'],
            'identifier' => ['required', 'string'],
            'version' => ['required', 'string'],
            'states' => ['array'],
            'smart' => ['nullable', 'array'],
            'smart.type' => ['nullable', 'string', 'in:relation_type,custom_logic,related_status'],
            'smart.options.relation_type' => ['required_if:smart.type,relation_type'],
            'smart.options.template' => ['required_if:smart.type,custom_logic'],
            'smart.options.status_type_reference' => ['required_if:smart.type,related_status'],
        ], [], [
            'reference' => 'Referenz',
            'default' => 'Initial-Wert',
            'description' => 'Beschreibung',
            'smart.type' => 'Typ',
            'smart.options.template' => 'Template',
            'smart.options.relation_type' => 'Verknüpfungstyp'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}

<?php

namespace App\Rules\ProcessType;

use App\Interfaces\PluginMeta;
use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateComponent
 * @package App\Rules\ProcessType
 */
class UpdateComponent implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    private Definition $definition;

    private Collection $actionTypeIds;

    private Collection $componentIds;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->actionTypeIds = $this->definition->actionTypes->pluck('id');
        $this->componentIds = $this->definition->actionTypes->pluck('components')->flatten()->pluck('id');
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
            'id' => ['required', 'uuid', Rule::in($this->componentIds)],
            'label' => ['nullable', 'string', 'max:50'],
            'css_classes' => ['nullable', 'string', 'max:250'],
            'width' => ['required', 'numeric', 'max:12'],
            'action_type_id' => ['required', 'uuid', Rule::in($this->actionTypeIds)],
            'options' => ['array']
        ]);

        // Sollten die Basisdaten validiert werden können, werden nun die Validierungsregeln der Componente
        // selbst geprüft.
        if ($validator->passes()) {
            $actionTypeId = $value['action_type_id'];
            $componentId = $value['id'];
            $component = $this->definition->actionType($actionTypeId)->component($componentId);

            /* @var PluginMeta $metaClass */
            $metaClass = $component->getPluginMetaClass();

            // Prüfen ob die Meta-Klasse mit den Methoden existiert.
            if (!$metaClass instanceof PluginMeta || !method_exists($metaClass, 'validationRules') || !method_exists($metaClass, 'validationMessages')) {
                $fail('Ungültige Command-Eingabedaten.');
            }

            // Aus der Meta-Datei die Regeln für das "options"-Attribut holen.
            $optionRules = $metaClass->validationRules($this->definition) ?? [];
            $optionMessages = $metaClass->validationMessages() ?? [];

            $attributes = [];

            foreach (array_keys($optionRules) as $field) {
                $attributes[$field] = 'Feld';
                $attributes[$field . '.*'] = 'Feld';
            }

            $validator = Validator::make($value['options'], $optionRules, $optionMessages, $attributes);

            if (!$validator->passes()) {
                $fail($validator->messages());
            }
        }
        else {
            $fail($validator->messages());
        }
    }

}

<?php

namespace Resources\Plugins\Allisa\ActionTypeComponent\Form\v1_0_0\configuration;

use App\Interfaces\PluginMeta;
use App\ProcessType\Definition;
use App\Rules\ValidInputOutputName;

/**
 * Unterstützende Klasse für serverseitige Validierung von Komponenten-Optionen.
 * Class Meta
 */
class Meta implements PluginMeta {

    /**
     * Gibt die Validierungsregeln für das "options"-Array einer Aktionstyp- oder StatusTyp-Komponente zurück. TEST
     * @param Definition $definition
     * @return array
     */
    public function validationRules(Definition $definition): array {
        return [
            'sets' => ['array'],
            'sets.*.label' => ['nullable', 'string', 'max:500'],
            'sets.*.css_classes' => ['nullable', 'string', 'max:250'],
            'sets.*.width' => ['required', 'numeric', 'max:12'],
            'sets.*.fields' => ['array'],
            'sets.*.fields.*' => ['array'],
            'sets.*.fields.*.type' => ['required', 'string'],
            'sets.*.fields.*.name' => ['required', 'string', 'max:60', 'distinct', new ValidInputOutputName],
            'sets.*.fields.*.label' => ['nullable', 'string'],
            'sets.*.fields.*.helper_text' => ['nullable', 'string', 'max:500'],
            'sets.*.fields.*.css_classes' => ['nullable', 'string', 'max:250'],
            'sets.*.fields.*.default' => ['nullable', 'string'],
            'sets.*.fields.*.width' => ['required', 'numeric', 'min:1', 'max:12'],
            'sets.*.multiple' => ['nullable', 'array'],
            'sets.*.multiple.enabled' => ['boolean'],
            'sets.*.multiple.readonly' => ['boolean'],
            'sets.*.multiple.min' => ['integer', 'min:0', 'max:50', 'lte:sets.*.multiple.max'],
            'sets.*.multiple.max' => ['integer', 'min:0', 'max:50', 'gte:sets.*.multiple.min'],
            'display' => ['array']
        ];
    }

    /**
     * Fehlermeldungen.
     * @return array
     */
    public function validationMessages(): array {
        return [
            'sets.*.fields.*.name.distinct' => 'Dieser Feldname existiert bereits.'
        ];
    }

}

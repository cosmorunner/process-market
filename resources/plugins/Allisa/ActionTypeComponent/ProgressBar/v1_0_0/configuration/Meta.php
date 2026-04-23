<?php

namespace Resources\Plugins\Allisa\ActionTypeComponent\ProgressBar\v1_0_0\configuration;

use App\Interfaces\PluginMeta;
use App\ProcessType\Definition;

/**
 * Unterstützende Klasse für serverseitige Validierung von Komponenten-Optionen.
 * Class Meta
 */
class Meta implements PluginMeta {

    /**
     * Gibt die Validierungsregeln für das "options"-Array einer Aktionstyp- oder StatusTyp-Komponente zurück.
     * @param Definition $definition
     * @return array
     */
    public function validationRules(Definition $definition): array {
        return [
            'type' => ['in:progress_bar,icons'],
            'label' => ['nullable', 'string', 'max:64'],
            'value' => ['required'],
            'min' => ['required'],
            'max' => ['required'],
            'color' => ['required'],
            'show_value' => ['boolean'],
            'icon' => ['nullable', 'string']
        ];
    }

    /**
     * Fehlermeldungen.
     * @return array
     */
    public function validationMessages(): array {
        return [];
    }

}

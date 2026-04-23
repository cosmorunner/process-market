<?php

namespace Resources\Plugins\Allisa\ActionTypeComponent\FilePreview\v1_0_0\configuration;

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
    public function validationRules(Definition $definition):array {
        return [
            'value' => ['required', 'string'],
            'show_download' => ['required', 'boolean'],
            'show_empty' => ['required', 'boolean'],
            'css_max_height' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Fehlermeldungen.
     * @return array
     */
    public function validationMessages():array {
        return [];
    }

}

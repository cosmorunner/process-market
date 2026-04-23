<?php

namespace Resources\Plugins\Allisa\ActionTypeComponent\Collection\v1_0_0\configuration;

use App\Interfaces\PluginMeta;
use App\ProcessType\Definition;
use Illuminate\Validation\Rule;

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
            'list_config_id' => ['required', Rule::in($definition->listConfigs->pluck('id'))],
            'display' => ['array']
        ];
    }

    /**
     * Fehlermeldungen.
     * @return array
     */
    public function validationMessages(): array {
        return [
            'list_config_id.in' => 'Die Listenkonfiguration existiert nicht.'
        ];
    }

}

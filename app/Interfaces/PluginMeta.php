<?php

namespace App\Interfaces;

use App\ProcessType\Definition;

/**
 * Weitere Metadaten-Helferfunktionen für Plugins.
 */
interface PluginMeta {

    /**
     * Gibt für Plugin-Optionen die Validierungs-Regeln zurück.
     * @param Definition $definition
     * @return array
     */
    public function validationRules(Definition $definition): array;

    /**
     * Messages für die Validierungsregeln.
     * @return array
     */
    public function validationMessages(): array;
}

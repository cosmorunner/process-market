<?php

namespace Resources\Plugins\Utils\Error;

use App\Plugins\BasePlugin;

/**
 * Wird als Platzhalter zurückgegeben, wenn ein gesuchtes Plugin nicht existiert oder es bei der Plugin-Setup-Methode
 * zu einem Error gekommen ist.
 * @package App\Plugins\Utils\Error
 */
class Plugin extends BasePlugin {

    /**
     * Weitere Initialisierung des Plugins.
     * @param array $componentOptions
     * @param null $data
     * @return mixed
     */
    protected function setup(array $componentOptions, $data = null): void {
        // Ignore
    }

    /**
     * Daten-Array, welches in die Vue-Komponente als Prop gegeben wird.
     * @return array
     */
    protected function data(): array {
        return [];
    }
}

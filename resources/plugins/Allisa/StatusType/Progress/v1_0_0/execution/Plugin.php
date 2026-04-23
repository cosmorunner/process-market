<?php

namespace Resources\Plugins\Allisa\StatusType\Progress\v1_0_0\execution;

use App\Plugins\StatusTypeBasePlugin;

/**
 * Class Progess
 * @package Resources\Plugins\Allisa\StatusType\Progress\v1_0_0\execution
 */
class Plugin extends StatusTypeBasePlugin {

    /**
     * Optionen des Plugins.
     * Ermöglicht dem Entwickler, neben den in der Konfiguration definierten Optionen weitere hinzuzufügen oder
     * diese vorher zu bearbeiten.
     * @param array $options
     * @return array
     */
    protected function options(array $options) {
        $options['min'] = (float) $this->pluggable->minValue();
        $options['max'] = (float) $this->pluggable->maxValue();

        return parent::options($options);
    }

    /**
     * Weitere Initialisierung des Plugins.
     * @param array $componentOptions
     * @param $data
     * @return mixed
     */
    protected function setup(array $componentOptions, $data = null): void {
        parent::setup($componentOptions, $data);
    }

}

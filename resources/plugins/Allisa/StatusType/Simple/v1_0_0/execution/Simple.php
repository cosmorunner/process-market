<?php

namespace Resources\Plugins\Allisa\StatusType\Simple\v1_0_0\execution;

use App\Plugins\StatusTypePlugin;

/**
 * Class Simple
 * @package Resources\Plugins\Allisa\StatusType\Simple\v1_0_0\execution
 */
class Simple extends StatusTypePlugin {

    /**
     * Weitere Initialisierung des Plugins.
     * @param array $componentOptions
     * @param $data
     * @return mixed
     */
    protected function setup(array $componentOptions, $data = null) : void {
        parent::setup($componentOptions, $data);
    }

    /**
     * @inheritDoc
     */
    protected function vueData() : array {
        return parent::vueData();
    }
}

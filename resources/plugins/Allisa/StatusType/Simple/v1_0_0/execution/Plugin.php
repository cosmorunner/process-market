<?php

namespace Resources\Plugins\Allisa\StatusType\Simple\v1_0_0\execution;

use App\Plugins\StatusTypeBasePlugin;

/**
 * Class Simple
 * @package Resources\Plugins\Allisa\StatusType\Simple\v1_0_0\execution
 */
class Plugin extends StatusTypeBasePlugin {

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

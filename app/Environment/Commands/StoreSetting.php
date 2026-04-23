<?php

namespace App\Environment\Commands;

use App\Environment\Setting;
use App\Models\Environment;

/**
 * Class StoreSystemSetting
 * @package App\Environment\Commands
 */
class StoreSetting extends Command {

    /**
     * Erstellt eine Gruppen-Rolle
     * @return Environment
     */
    public function command(): Environment {
        $settings = $this->environment->blueprint->settings;
        $settings->add(Setting::make([
            'name' => $this->payload['name'] ?? '',
            'value' => $this->payload['value'] ?? '',
            'owner_id' => $this->payload['owner_id'] ?? null,
            'owner_type' => $this->payload['owner_type'] ?? null
        ]));

        $this->environment->updateBlueprint('settings', $settings);

        return $this->environment;
    }
}

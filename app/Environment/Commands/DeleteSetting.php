<?php

namespace App\Environment\Commands;

use App\Environment\Setting;
use App\Models\Environment;

/**
 * Löschen eines System-Einstellung aus dem Blueprint.
 * Class DeleteSystemSetting
 * @package App\Environment\Commands
 */
class DeleteSetting extends Command {

    /**
     * Löschen eines Benutzers.
     * @return Environment
     */
    public function command(): Environment {
        $settings = $this->environment->blueprint->settings->filter(fn(Setting $systemSetting) => $systemSetting->owner_id !== $this->payload['owner_id'] ?? null);
        $this->environment->updateBlueprint('settings', $settings);

        return $this->environment;
    }
}

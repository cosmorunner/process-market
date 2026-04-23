<?php

namespace App\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\Template;

/**
 * Class UpdateTemplate
 * @package App\ProcessType\Commands
 */
class UpdateTemplate extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['templates'];

    /**
     * Template aktualisieren
     * @return Definition
     */
    public function command(): Definition {
        $this->definition->templates = $this->definition->templates->map(function (Template $template) {
            if ($this->payload['id'] === $template->id) {
                $payload = $this->payload;

                return Template::make($payload);
            }

            return $template;
        });

        return $this->definition;
    }

    /**
     * Update the preview datasets of the templates.
     * @param ProcessVersion $processVersion
     * @param array $payload
     * @return void
     */
    public function afterCommandExecution(ProcessVersion $processVersion, array $payload): void {
        $processVersion->syncTemplatePreviewDatasets();
    }

}

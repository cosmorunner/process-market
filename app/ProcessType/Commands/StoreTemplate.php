<?php

namespace App\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\Models\Template as TemplateModel;
use App\ProcessType\Definition;
use App\ProcessType\Template;

/**
 * Class StoreTemplate
 * @package App\ProcessType\Commands
 */
class StoreTemplate extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['templates'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $template = TemplateModel::find($this->payload['template']);

        if (!$template) {
            return $this->definition;
        }

        $data = $template->data;

        $template = Template::make([
            'name' => $this->payload['name'],
            'type' => $template->type,
            'data' => $data,
            'mapping' => $template->mapping,
            'stylesheets' => ['bootstrap-4-6']
        ]);

        $this->definition->templates->add($template);

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

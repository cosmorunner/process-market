<?php

namespace App\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\Template;
use App\Traits\DeletesProcessorModelUsage;

/**
 * Class DeleteTemplate
 * @package App\ProcessType\Commands
 */
class DeleteTemplate extends Command {

    use DeletesProcessorModelUsage;

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
        $definition = $this->definition;
        $definition->templates = $this->definition->templates
            ->filter(fn(Template $template) => $template->id !== $this->payload['id'])->values();

        return $definition;
    }


    /**
     * Nutzung des Templates bei den Aktionstypen entfernen.
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    public function afterExecutingCommands(Definition $updatedDefinition): array {
        $templateId = $this->payload['id'];

        return $this->deleteProcessorModelUsages($updatedDefinition, 'template', $templateId);
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

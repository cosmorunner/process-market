<?php

namespace App\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\Template;
use Ramsey\Uuid\Uuid;

/**
 * Class CopyTemplate
 * @package App\ProcessType\Commands
 */
class CopyTemplate extends Command {

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
        $template = Template::make(array_merge($this->payload, [
            'id' => Uuid::uuid4()->toString(),
            'name' => $this->payload['name'] . ' Kopie'
        ]));

        $definition->templates->add($template);

        return $definition;
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

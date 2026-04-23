<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\Processor;
use Illuminate\Support\Collection;

/**
 * Class DeleteProcessor
 * @package App\ProcessType\Commands
 */
class DeleteProcessor extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return mixed
     */
    public function command(): Definition {
        $actionType = $this->definition->actionType($this->payload['action_type_id']);
        $processors = $actionType->processors->filter(fn(Processor $processor) => $processor->id !== $this->payload['id']);

        // Nutzungen des Prozessors in anderen Prozessoren entfernen. "E-Mail versenden" nutzt z.B. als Anhang das Ergebnis des Prozessors "Dokument erzeugen.
        $processors = $this->deleteProcessorUsages($this->payload['id'], $processors);
        $actionType->processors = $processors->values();

        return $this->definition;
    }

    /**
     * Entfernt die Nutzung des gelöschten Prozessors in den Optionen der anderen Prozessoren.
     * Bei "send_email" kann z.B. bei den "attachments" der Wert "processor|2600d268-adfe-4a8b-8c60-826315bdce2c" gewählt werden.
     * @param $processorId
     * @param Collection $processors
     * @return Collection
     */
    public function deleteProcessorUsages($processorId, Collection $processors) {
        $pipeNotation = 'processor|' . $processorId;

        foreach ($processors as $processor) {
            foreach ($processor->options as $key => $value) {
                if ($processor->options[$key] === $pipeNotation) {
                    $processor->options[$key] = null;
                }
                if (is_array($processor->options[$key])) {
                    $processor->options[$key] = array_filter($processor->options[$key], fn($item) => $item !== $pipeNotation);
                }
            }
        }

        return $processors;
    }

}

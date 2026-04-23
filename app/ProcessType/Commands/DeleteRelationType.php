<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\RelationType;
use Illuminate\Support\Collection;

/**
 * Class DeleteRelationType
 * @package App\ProcessType\Commands
 */
class DeleteRelationType extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['relation_types'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $definition = $this->definition;
        $definition->relationTypes = $this->definition->relationTypes
            ->filter(fn(RelationType $relationType) => $relationType->id !== $this->payload['id'])->values();

        return $definition;
    }

    /**
     * Nutzung des Verknüpfungstyp in alles Aktionstyp-Prozessoren entfernen
     * @param Definition $updatedDefinition
     * @return Command[]
     */
    protected function afterExecutingCommands(Definition $updatedDefinition): array {
        $actionTypes = $updatedDefinition->actionTypes;
        $commands = [];

        foreach ($actionTypes as $actionType) {
            $actionType->processors = $this->deleteRelationTypeUsages($this->payload['id'], $actionType->processors);
            $commands[] = new UpdateActionType($actionType->toArray(), $updatedDefinition, $this->processVersion);
        }

        return $commands;
    }

    /**
     * Entfernt die Nutzung des gelöschten Verknüpfungstyps in den Optionen von Prozessoren.
     * @param $relationTypeId
     * @param Collection $processors
     * @return Collection
     */
    public function deleteRelationTypeUsages($relationTypeId, Collection $processors) {
        $pipeNotation = 'relationType|' . $relationTypeId;

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

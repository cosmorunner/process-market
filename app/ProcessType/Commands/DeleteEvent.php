<?php

namespace App\ProcessType\Commands;

use App\Loaders\PipeLoader;
use App\ProcessType\Definition;
use App\ProcessType\Event;
use App\ProcessType\Processor;
use Illuminate\Support\Collection;

/**
 * Class DeleteEvent
 * @package App\ProcessType\Commands
 */
class DeleteEvent extends Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = ['events'];

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $definition = $this->definition;
        $definition->events = $this->definition->events->filter(fn(Event $event) => $event->id !== $this->payload['id'])
            ->values();

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
            $actionType->processors = $this->deleteEventUsages($this->payload['name'], $actionType->processors);
            $commands[] = new UpdateActionType($actionType->toArray(), $updatedDefinition, $this->processVersion);
        }

        return $commands;
    }

    /**
     * Entfernt die Nutzung des gelöschten Verknüpfungstyps in den Optionen von Prozessoren.
     * @param $eventName
     * @param Collection $processors
     * @return Collection
     */
    public function deleteEventUsages($eventName, Collection $processors) {
        return $processors->filter(function (Processor $processor) use ($eventName) {
            if ($processor->identifier != 'trigger_event') {
                return true;
            }

            $eventOption = $processor->options['event'] ?? '';

            if (!PipeLoader::isProcessVersionModel($eventOption)) {
                return true;
            }

            $eventOptionName = PipeLoader::getKey($eventOption);

            return $eventOptionName !== $eventName;
        })->values();
    }

}

<?php


namespace App\Traits;


use App\ProcessType\Commands\Command;
use App\ProcessType\Commands\UpdateActionType;
use App\ProcessType\Definition;
use Illuminate\Support\Collection;

/**
 * Löscht die Nutzung eines Models in den Prozessor-Optionen von Aktionstypen indem UpdateActionType-Commands
 * zurückgegeben werden.
 * Trait DeletesProcessorModelUsage
 * @package App\Traits
 */
trait DeletesProcessorModelUsage {

    /**
     * Löscht die Nutzung eines Models in den Prozessor-Optionen von Aktionstypen indem UpdateActionType-Commands
     * zurückgegeben werden.
     * @param Definition $definition
     * @param string $modelName
     * @param string $id
     * @return array
     */
    protected function deleteProcessorModelUsages(Definition $definition, string $modelName, string $id) {
        $commands = [];
        $pipeNotation = $modelName . '|' . $id;

        /* @var Command $this */
        foreach ($definition->actionTypes as $actionType) {
            $actionType->processors = $this->deleteProcessorModelUsage($pipeNotation, $actionType->processors);
            $commands[] = new UpdateActionType($actionType->toArray(), $definition, $this->processVersion);
        }

        return $commands;
    }

    /**
     * Entfernt die Nutzung des Models in den Optionen von Prozessoren.
     * @param string $pipeNotation
     * @param Collection $processors
     * @return Collection
     */
    public function deleteProcessorModelUsage(string $pipeNotation, Collection $processors) {
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

<?php

namespace App\ProcessType\Commands;

use App\Exceptions\CommandDoesNotExist;
use App\Models\ProcessVersion;
use App\ProcessType\Definition;

/**
 * Abstrakte Klasse für die Prozesstyp-Commands
 * Class Command
 * @package App\ProcessType
 */
abstract class Command {

    /**
     * Definition-Array-Keys die durch den Command aktualisiert werden.
     * Nur jede Keys werden nach dem Command zurückgegeben, für eine verbesserte Performance.
     * Wenn leer wird alles zurückgegeben.
     * @var array
     */
    const AFFECTS_PARTS = [];

    /**
     * Die Liste aller ausgeführten Commands.
     */
    public array $executedCommands = [];

    /**
     * Payload mit dem der Command ausgeführt wird.
     */
    protected array $payload;

    /**
     * Graph-Definition, welche durch den Command bearbeitet wird.
     */
    protected Definition $definition;

    protected ?ProcessVersion $processVersion;

    /**
     * @var string Model-Id, welches an die Stelle des "Rechtsklicks" auf dem Graphen positioniert werden soll.
     */
    protected $positionModelId = null;

    /**
     * @var boolean Flagge, ob der Command im Kontext eines Statustypen ausgeführt wurde.
     */
    protected $statusTypeContext = null;

    /**
     * Flag indicating whether the graph must be recalculated after the command. For example, for StoreActionType or DeleteActionRule
     * the graph must be recalculated.
     * @var bool
     */
    public $recalculate = false;

    /**
     * Command constructor.
     * @param array $payload
     * @param Definition $definition
     * @param ProcessVersion $processVersion
     */
    public final function __construct(array $payload, Definition $definition, ProcessVersion $processVersion) {
        $this->payload = $payload;
        $this->definition = $definition;
        $this->processVersion = $processVersion;
    }

    /**
     * Gibt den Command zurück.
     * @return Definition
     */
    abstract protected function command(): Definition;

    /**
     * Gibt zusätzliche Commands zurück die vor der Command-Ausführung ausgeführt werden sollen.
     * @param Definition $definition
     * @return Command[]
     * @noinspection PhpUnusedParameterInspection
     */
    protected function beforeExecutingCommands(Definition $definition): array {
        return [];
    }

    /**
     * Gibt zusätzliche Commands zurück die ebenfalls nach der Command-Ausführung ausgeführt werden sollen.
     * @param Definition $updatedDefinition Die bereits durch den ursprünglichen Command aktualisierte Definition
     * @return Command[]
     */
    protected function afterExecutingCommands(Definition $updatedDefinition): array {
        return [];
    }

    /**
     * Method called after the command was executed. Is only called on the "main" command, not called on commands that are
     * added via the "beforeExecutingCommands" and "afterExecutingCommands".
     * @param ProcessVersion $processVersion Updated process version after command execution.
     * "definition" was already updated at this point.
     * @param array $payload Original payload of the command.
     * @return void
     */
    public function afterCommandExecution(ProcessVersion $processVersion, array $payload): void {
    }

    /**
     * Ausführung des Commands und der zusätzlich Commands.
     * @return Definition
     */
    final function execute(): Definition {
        // Die konkrete Klasse hinzufügen.
        $this->executedCommands = [$this::class];

        // Optionale Commands vor dem eigentlichen Command ausführen.
        foreach ($this->beforeExecutingCommands($this->definition) as $beforeExecutingCommand) {
            if ($beforeExecutingCommand instanceof Command) {
                $this->definition = $beforeExecutingCommand->execute();
                $this->executedCommands[] = $beforeExecutingCommand::class;
            }
        }

        // Den Command ausführen.
        $this->definition = $this->command()->afterConstruct();

        // Weiteren optionale Commands ausführen mit der aktualisieren Definition.
        foreach ($this->afterExecutingCommands($this->definition) as $afterExecutingCommand) {
            if ($afterExecutingCommand instanceof Command) {
                $this->definition = $afterExecutingCommand->execute();
                $this->executedCommands[] = $afterExecutingCommand::class;
            }
        }

        return $this->definition->afterConstruct();
    }

    /**
     * Erzeugt eine Command Instanz.
     * @param string $name
     * @param array $payload
     * @param Definition $definition
     * @param ProcessVersion $processVersion
     * @return Command
     * @throws CommandDoesNotExist
     */
    public static function create(string $name, array $payload, Definition $definition, ProcessVersion $processVersion) {
        $commandClass = config('app.process_type_commands_namespace') . $name;

        if (!class_exists($commandClass)) {
            throw new CommandDoesNotExist($name);
        }

        return new $commandClass($payload, $definition, $processVersion);
    }

    /**
     * Gibt das Model zurück, welches an die Stelle des "Rechtsklicks" auf dem Graphen positioniert werden soll.
     * @return string|null
     */
    public function getPositionModelId() {
        return $this->positionModelId;
    }

    /**
     * Gibt zurück ob der Command im Kontext eines Statustypen ausgeführt wurde.
     * @return bool|null
     */
    public function getStatusTypeContext() {
        return $this->statusTypeContext;
    }

    /**
     * In dem Fall, dass bei einem Command keine "position" mitgegeben wurde, wird es dem auszuführenden Command überlassen,
     * gegebenenfalls eine Graph-Position zu bestimmen. Z.b. beim Anlegen einer neuen Aktion über den "Hinzufügen"-Button.
     * @return array
     */
    public function preferredGraphPosition(): array {
        return [];
    }

    /**
     * Update newly created nodes positions based on preferredGraphPosition or on command type.
     * @param array $position
     * @param array $newCalculated
     * @return array
     */
    public function updateGraphPositions(array $position, array $newCalculated) {
        // Wenn eine neue Node (z.B. Statustyp, Aktion oder Zustand) erstellt wurde, wurde eine gewünschte Position
        // im Graphen mitgegeben (position), welche hier gesetzt wird.
        if (($position['x'] ?? null) && ($position['y'] ?? null)) {
            foreach ($newCalculated as $key => $item) {
                $data = $item['data'];
                $modelId = $data['model_id'] ?? null;

                // Anhang des "Rechtsklicks" auf dem Graphen, wird versucht die erzeugte Node zu platzieren.
                if ($this->getPositionModelId() && $modelId && $modelId === $this->getPositionModelId()) {
                    if (!is_null($this->getStatusTypeContext()) && array_key_exists('status_type_id', $data)) {
                        if ($data['status_type_id'] === $this->getStatusTypeContext()) {
                            $item['position'] = $position;
                            $newCalculated[$key] = $item;
                        }
                        continue;
                    }
                    $item['position'] = $position;
                    $newCalculated[$key] = $item;
                }
                // Update position of all date-elements that are within the statustype that has been created in this command
                if ($this instanceof StoreStatusType && $data['type'] === 'state' && $this->getPositionModelId() === $data['status_type_id']) {
                    $item['position'] = $position;
                    $newCalculated[$key] = $item;
                }
            }
        }

        return $newCalculated;
    }
}

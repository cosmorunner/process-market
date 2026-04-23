<?php


namespace App\Environment\Commands;

use App\Exceptions\CommandDoesNotExist;
use App\Models\Environment;

/**
 * Abstrakte Klasse für die Prozesstyp-Commands
 * Class Command
 * @package App\ProcessType
 */
abstract class Command {

    /**
     * Payload mit dem der Command ausgeführt wird.
     */
    protected array $payload;

    protected Environment $environment;

    /**
     * Command constructor.
     * @param array $payload
     * @param Environment $environment
     */
    public final function __construct(array $payload, Environment $environment) {
        $this->payload = $payload;
        $this->environment = $environment;
    }

    /**
     * Gibt den Command zurück.
     * @return Environment
     */
    abstract protected function command(): Environment;

    /**
     * Gibt zusätzliche Commands zurück die vor der Command-Ausführung ausgeführt werden sollen.
     * @param Environment $environment
     * @return Command[]
     */
    protected function beforeExecutingCommands(Environment $environment): array {
        return [];
    }

    /**
     * Gibt zusätzliche Commands zurück die ebenfalls nach der Command-Ausführung ausgeführt werden sollen.
     * @param Environment $environment
     * @return Command[]
     */
    protected function afterExecutingCommands(Environment $environment): array {
        return [];
    }

    /**
     * Ausführung des Commands und der zusätzlich Commands.
     * @return Environment
     */
    final function execute(): Environment {
        // Optionale Commands vor dem eigentlichen Command ausführen.
        foreach ($this->beforeExecutingCommands($this->environment) as $afterExecutingCommand) {
            if ($afterExecutingCommand instanceof Command) {
                $this->environment = $afterExecutingCommand->execute();
            }
        }

        // Zuerst den eigenen Command ausführen und Blueprint auf das Environemnt schreiben.
        $this->environment = $this->command();

        // Weiteren optionale Commands ausführen mit dem aktualisieren Blueprint.
        foreach ($this->afterExecutingCommands($this->environment) as $afterExecutingCommand) {
            if ($afterExecutingCommand instanceof Command) {
                $this->environment = $afterExecutingCommand->execute();
            }
        }

        return $this->environment;
    }

    /**
     * Erzeugt eine Command Instanz.
     * @param string $name
     * @param array $payload
     * @param Environment $environment
     * @return Command
     * @throws CommandDoesNotExist
     */
    public static function create(string $name, array $payload, Environment $environment) {
        $commandClass = config('app.environment_blueprint_commands_namespace') . $name;

        if (!class_exists($commandClass)) {
            throw new CommandDoesNotExist($name);
        }

        return new $commandClass($payload, $environment);
    }

}

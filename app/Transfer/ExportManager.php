<?php /** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */


namespace App\Transfer;

use App\Exceptions\TransferException;
use App\Http\Resources\ProcessFileDefinition;
use App\Models\ProcessVersion;

/**
 * Regelt den Export eines Prozesstyps inklusive Definition-Export.
 * Class ExportManager
 * @package App\Utils
 */
class ExportManager {

    /**
     * Export-Modus bei dem ein Prozess zu einer .json-Datei exportiert werden soll.
     */
    const MODE_FILE_EXPORT = 'MODE_FILE_EXPORT';

    /**
     * Export-Modus bei dem ein Prozes zu einer Array-Definition (z.b. für einen bei API-Auftruf) exportiert werden soll.
     */
    const MODE_JSON_EXPORT = 'MODE_ARRAY_EXPORT';

    /**
     * Existierende Manager-Modes.
     */
    const AVAILABLE_MODES = [self::MODE_FILE_EXPORT, self::MODE_JSON_EXPORT];

    /**
     * Export-Modus des Managers.
     * @var string
     */
    private string $mode = '';

    private ProcessVersion $processVersion;

    /**
     * Liest Prozesstyp-Daten ein bzw. versucht anhand eines Namespaces (z.B. allisa/demo@1.0.0) einen Prozesstyp
     * zu ermitteln.
     * ProcessTypeTransferManager constructor.
     * @param $fullNamespaceWithVersion
     * @param string $managerMode
     * @throws TransferException
     */
    public function __construct($fullNamespaceWithVersion, string $managerMode) {
        if (!in_array($managerMode, self::AVAILABLE_MODES)) {
            throw new TransferException('Ungültiger Export-Modus.');
        }

        /* @var ProcessVersion $processVersion */
        if (!$processVersion = ProcessVersion::findByFullNamespace($fullNamespaceWithVersion)) {
            throw new TransferException('Der Prozesstyp ' . $fullNamespaceWithVersion . ' existiert nicht.');
        }

        $this->mode = $managerMode;
        $this->processVersion = $processVersion;
    }

    /**
     * Exportiert den Prozesstyp zum angegeben Modus. Bei self::MODE_FILE_EXPORT wird der Pfad relativ zum Storage zur Datei zurückgegeben.
     * @return ProcessFileDefinition|string|false
     */
    private function export() {
        return match ($this->mode) {
            self::MODE_FILE_EXPORT => $this->processVersion->exportDefinition(),
            self::MODE_JSON_EXPORT => new ProcessFileDefinition($this->processVersion),
            default => null,
        };
    }

    /**
     * Exportiert die Prozesstyp-Version zu einer Datei.
     * @param $fullNamespaceWithVersion
     * @return string Filename in "process_types" directory.
     */
    public static function exportToFile($fullNamespaceWithVersion) {
        return (new static($fullNamespaceWithVersion, self::MODE_FILE_EXPORT))->export();
    }

    /**
     * Exportiert die Prozesstyp-Version zu JSON.
     * @param $fullNamespaceWithVersion
     * @return ProcessFileDefinition
     */
    public static function exportToJson($fullNamespaceWithVersion) {
        return (new static($fullNamespaceWithVersion, self::MODE_JSON_EXPORT))->export();
    }
}

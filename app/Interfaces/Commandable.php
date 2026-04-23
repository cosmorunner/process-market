<?php


namespace App\Interfaces;


use App\ProcessType\Definition;

/**
 * Interface Commandable
 * Wird für die jeweiligen Änderungen am Prozesstyp genutzt, wie z.B. Statustyp hinzufügen oder Aktionsregel ändern.
 * Siehe App\ProcessType\Commands
 * @package App\Interfaces
 */
interface Commandable {

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @param array $payload Daten für die Ausführung des Commands.
     * @return Definition
     */
    public function execute(array $payload);

    /**
     * Gibt das Model zurück, welches an die Stelle des "Rechtsklicks" auf dem Graphen positioniert werden soll.
     * @return string|null
     */
    public function getPositionModelId();

    /**
     * Gibt die Statustyp-Id zurück, wenn der Command im Kontext eines Statustypen ausgeführt wurde, z.B. Aktionsregel erstellen.
     * @return string|null
     */
    public function getStatusTypeContext();
}

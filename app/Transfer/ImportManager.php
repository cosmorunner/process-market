<?php

namespace App\Transfer;

use App\ProcessType\Definition;
use Illuminate\Support\Facades\Storage;

/**
 * Regelt den Import eines Prozesstyps inklusive Definition-Export und optionaler Lizenz-Export.
 * Class ExportManager
 * @package App\Utils
 */
class ImportManager {

    /**
     * Vollständiger Name des Prozesses der aus der exportierten Datei eingelesen werden soll.
     * @var string
     */
    private string $fullNamespaceWithVersion;

    private function __construct(string $fullNamespaceWithVersion) {
        $this->fullNamespaceWithVersion = $fullNamespaceWithVersion;
    }

    /**
     * Vollständigen Storage-Pfad zur Datei.
     * @return string
     */
    public function filePath(): string {
        return config('app.process_types_dir') . '/' . $this->fileName();
    }

    /**
     * Gibt den Namen der Datei zurück, wenn diese Prozess-Version exportiert werden würde.
     * @return string
     */
    public function fileName(): string {
        $parts = explode('/', $this->fullNamespaceWithVersion);
        $namespace = $parts[0];
        $versionParts = explode('@', $parts[1]);
        $identifier = $versionParts[0];
        $version = $versionParts[1];

        return $namespace . '_' . $identifier . '@' . str_replace('.', '-', $version) . '.json';
    }

    /**
     * @param string $fullNamespaceWithVersion
     * @return Definition|null
     */
    public static function readDefinition(string $fullNamespaceWithVersion): Definition|null {
        $self = new static($fullNamespaceWithVersion);
        $path = $self->filePath();

        if (!Storage::exists($path)) {
            return null;
        }

        $content = json_decode(Storage::get($path), true);

        if (is_array($content) && array_key_exists('definition', $content)) {
            return new Definition($content['definition']);
        }

        return null;
    }
}

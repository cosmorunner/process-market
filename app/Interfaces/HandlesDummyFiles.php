<?php

namespace App\Interfaces;

/**
 * Ersetzt die {{dummy.file.pdf}}-Syntaxen mit tatsächlichen Dummy-Dateien für die Aktionsausführung.
 * Interface HandlesDummyFiles
 * @package App\Interfaces
 */
interface HandlesDummyFiles {

    /**
     * Ersetzt die {{}}-Syntax-Werte mit den Dummy Dateien
     * @param array $data
     * @return array
     */
    public function insertsDummyFiles(array $data): array;
}

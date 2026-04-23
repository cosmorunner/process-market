<?php

namespace App\Traits;

/**
 * Ersetzt die {{dummy.file.pdf}}-Syntaxen mit tatsächlichen Dummy-Dateien für die Aktionsausführung.
 * Trait UsesDummyFiles
 * @package App\Traits
 */
trait UsesDummyFiles {

    /**
     * Ersetzt die {{}}-Syntax-Werte mit den Dummy Dateien
     * @param array $data
     * @return array
     */
    public function insertsDummyFiles(array $data): array {
        foreach ($data as $name => $value) {
            if (is_string($value)) {
                preg_match_all('/{{([^}]+[a-z\d_]+)}}/', $value, $matches);
                if (count($matches[0])) {
                    switch ($matches[0][0]) {
                        case '{{dummy.file.pdf}}':
                            $data[$name] = fopen(storage_path('app/demo-dokument.pdf'), 'r');
                            break;
                        case '{{dummy.file.image}}':
                            $data[$name] = fopen(storage_path('app/demo-bild.jpg'), 'r');
                            break;
                        case '{{dummy.file.excel}}':
                            $data[$name] = fopen(storage_path('app/demo-excel.xlsx'), 'r');
                            break;
                    }
                }
            }

            if (is_array($value)) {
                foreach ($value as $index => $item) {
                    if (is_string($item)) {
                        preg_match_all('/{{([^}]+[a-z\d_]+)}}/', $item, $matches);
                        if (count($matches[0])) {
                            switch ($matches[0][0]) {
                                case '{{dummy.file.pdf}}':
                                    $data[$name][$index] = fopen(storage_path('app/demo-dokument.pdf'), 'r');
                                    break;
                                case '{{dummy.file.image}}':
                                    $data[$name][$index] = fopen(storage_path('app/demo-bild.jpg'), 'r');
                                    break;
                                case '{{dummy.file.excel}}':
                                    $data[$name][$index] = fopen(storage_path('app/demo-excel.xlsx'), 'r');
                                    break;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}

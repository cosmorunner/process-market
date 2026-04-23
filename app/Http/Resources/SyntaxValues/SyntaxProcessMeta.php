<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxProcessMeta
 * Basis Syntax-Werte die einem Benutzer zur Verfügung stehen, wie z.B. Datumsformate oder System-Daten
 * @package App\Http\Resources
 */
class SyntaxProcessMeta extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            (array) new Item('Prozess-Meta - Id', '[[process.meta.id]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Model-Pipe-Notation', '[[process.meta.pipe_notation]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Name', '[[process.meta.name]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Beschreibung', '[[process.meta.description]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Referenz', '[[process.meta.reference]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Tags', '[[process.meta.tags]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Icon', '[[process.meta.image]]', 'process.meta'),
            (array) new Item('Prozess-Meta - URL', '[[process.meta.url]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Erstelldatum', '[[process.meta.created_at]]', 'process.meta'),
            (array) new Item('Prozess-Meta - Aktualisierungsdatum', '[[process.meta.updated_at]]', 'process.meta'),
        ];
    }
}

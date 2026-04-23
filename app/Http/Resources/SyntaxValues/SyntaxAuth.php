<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxAuth
 * Auth-Werte
 * @package App\Http\Resources
 */
class SyntaxAuth extends JsonResource {

    /**
     * @var ProcessVersion
     */
    public $resource;

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            (array) new Item('Benutzer - Pipe-Notation', '[[auth.pipe_notation]]', 'auth'),
            (array) new Item('Benutzer - Prozess-Identität Pipe-Notation', '[[auth.identity]]', 'auth'),
            (array) new Item('Benutzer - E-Mail', '[[auth.email]]', 'auth'),
            (array) new Item('Benutzer - Vorname', '[[auth.first_name]]', 'auth'),
            (array) new Item('Benutzer - Nachname', '[[auth.last_name]]', 'auth'),
            (array) new Item('Benutzer - Vor- und Nachname', '[[auth.full_name]]', 'auth'),
            (array) new Item('Benutzer - Prozess-Identität Id', '[[auth.identity_id]]', 'auth'),
            (array) new Item('Benutzer - Prozess-Rollen des Benutzers', '[[process.auth.roles]]', 'auth')
        ];
    }
}

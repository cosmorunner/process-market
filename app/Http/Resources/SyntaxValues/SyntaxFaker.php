<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxDate
 * Datumswerte
 * @package App\Http\Resources
 */
class SyntaxFaker extends JsonResource {

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
            (array) new Item('Zufälliger String - 6 Zeichen - A-Z', '[[faker.string.random(chars=alpha|length=6|case=upper)]]', 'faker'),
            (array) new Item('Zufälliger String - 6 Zeichen - a-z', '[[faker.string.random(chars=alpha|length=6|case=lower)]]', 'faker'),
            (array) new Item('Zufälliger String - 6 Zeichen - A-Z, 0-9', '[[faker.string.random(chars=alphanumeric|length=6|case=upper)]]', 'faker'),
            (array) new Item('Zufälliger String - 6 Zeichen - a-z, 0-9', '[[faker.string.random(chars=alphanumeric|length=6|case=lower)]]', 'faker'),
            (array) new Item('Zufälliger String - 6 Zeichen - 0-9', '[[faker.string.random(chars=numeric|length=6)]]', 'faker'),
            (array) new Item('Zufälliger String - UUID (v4)', '[[faker.string.uuid]]', 'faker'),
        ];
    }
}

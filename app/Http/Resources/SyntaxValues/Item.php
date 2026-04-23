<?php

namespace App\Http\Resources\SyntaxValues;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Item
 * Repräsentiert ein einzelnes Syntax Item.
 */
class Item implements Arrayable {

    /**
     * @param string $label e.g. Prozess-Daten - field_1
     * @param string $value e.g. [[process.outputs.field_1]]
     * @param string $part e.g. process.outputs
     */
    public function __construct(public string $label, public string $value, public string $part) {}

    /**
     * Array Repräsentation des Syntax-Items.
     * @return array
     */
    public function toArray() {
        return [
            'label' => $this->label,
            'value' => $this->value,
            'part' => $this->part
        ];
    }
}
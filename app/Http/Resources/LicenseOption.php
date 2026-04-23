<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LicenseOption
 * @package App\Http\Resources
 */
class LicenseOption extends JsonResource {

    /**
     * @var array{level: string, level_options: array}
     */
    public $resource;

    /**
     * Gibt den Namen einer Lizenz-Option zurück.
     * @param string $level
     * @return string
     */
    public static function getName(string $level) {
        return [
                'open-source' => 'Open-Source',
                'private' => 'Privat'
            ][$level] ?? $level;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'level' => $this->resource['level'],
            'level_options' => $this->resource['level_options'],
            'level_name' => self::getName($this->resource['level']),
            'price' => $this->resource['price'] ?? 0
        ];
    }
}

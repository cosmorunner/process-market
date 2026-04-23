<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Environment\Variable;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxVariables
 * Daten der System-Variablen.
 * @package App\Http\Resources
 */
class SyntaxVariables extends JsonResource {

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
        $environments = $this->resource->environments;
        $variables = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->variables)
            ->flatten()
            ->unique('identifier');

        $items = [];

        /* @var Variable $variable */
        foreach ($variables as $variable) {
            $items[] = (array)new Item('Variable - Wert - ' . $variable->identifier, '[[system.variables.' . $variable->identifier . '.value]]', 'variables');
            $items[] = (array)new Item('Variable - URL - ' . $variable->identifier, '[[system.urls.variables.' . $variable->identifier . ']]', 'variables');
        }

        return $items;
    }
}

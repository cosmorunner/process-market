<?php

namespace App\Http\Resources\SyntaxValues;

use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SyntaxGraphsUrls
 * Initialaktionen-Urls
 * @package App\Http\Resources
 */
class SyntaxGraphsUrls extends JsonResource {

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
        $definition = $this->resource->definition;
        $namespace = $definition->namespace;
        $identifier = $definition->identifier;
        $version = str_replace('.', '-', $this->resource->version);
        $latestNamespace = $definition->fullNamespace() . '@latest';

        $items = [];

        foreach ($definition->actionTypes as $actionType) {
            // Exakte Version
            $label = $definition->fullNamespaceWithVersion() . ' - Initialaktion-URL - ' . $actionType->reference;
            $value = '[[process_type.' . $namespace . '.' . $identifier . '.' . $version . '.urls.action_types.' . $actionType->reference . ']]';
            $items[] = (array) new Item($label, $value, 'graphs.urls');

            // Latest Version
            $label = $latestNamespace . ' - Initialaktion-URL - ' . $actionType->reference;
            $value = '[[process_type.' . $namespace . '.' . $identifier . '.latest.' . 'urls.action_types.' . $actionType->reference . ']]';
            $items[] = (array) new Item($label, $value, 'graphs.urls');
        }

        return $items;
    }
}

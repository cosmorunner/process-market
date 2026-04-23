<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Environment\Connector;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentRequests
 * @package App\Http\Resources
 */
class PipeEnvironmentRequests extends JsonResource {

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
        $items = [];
        $environments = $this->resource->environments;

        $connectors = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->connectors)
            ->flatten();

        /* @var \App\Environment\Request[] $requests */
        $requests = $environments->pluck('blueprint')->map(fn(Blueprint $blueprint) => $blueprint->requests)->flatten();

        foreach ($requests as $request) {
            /* @var Connector $connector */
            $connector = $connectors->firstWhere('id', '=', $request->connector_id);

            if ($connector) {
                $label = 'Connector - ' . $connector->identifier . ' - Request - ' . $request->identifier;
                $value = 'app::request|' . $request->identifier;
                $items[] = (array) new Item($label, $value, 'environment_requests');
            }
        }

        return $items;
    }
}

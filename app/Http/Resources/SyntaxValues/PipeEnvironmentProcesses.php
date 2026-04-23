<?php

namespace App\Http\Resources\SyntaxValues;

use App\Environment\Blueprint;
use App\Environment\Process;
use App\Models\ProcessVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PipeEnvironmentProcesses
 * @package App\Http\Resources
 */
class PipeEnvironmentProcesses extends JsonResource {

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
        $processes = $environments->pluck('blueprint')
            ->map(fn(Blueprint $blueprint) => $blueprint->processes)
            ->flatten()
            ->unique('id');

        return $processes->map(function (Process $process) {
            return new Item('Prozess - ' . $process->name, 'app::process|' . $process->id, 'environment_processes');
        })->toArray();
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\ListConfig;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class ListSupportData
 * @package App\Http\Resources
 */
class ListSupportData extends JsonResource {

    /**
     * @var ListConfig
     */
    public $resource;

    /**
     * @var ProcessVersion
     */
    public ProcessVersion $processVersion;

    /**
     * Only return specific parts of the list support data of the listconfig,
     * e.g. only the used select statements.
     * @var array|null
     */
    public array|null $parts;

    /**
     * Create a new resource instance.
     * @param ListConfig $listConfig
     * @param ProcessVersion $processVersion
     * @param array|null $parts
     */
    public function __construct(ListConfig $listConfig, ProcessVersion $processVersion, array $parts = null) {
        $this->processVersion = $processVersion;
        $this->parts = $parts;

        parent::__construct($listConfig);
    }

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request) {
        $processes = $this->processVersion->process->author->publishedProcesses();
        $licensedProcesses = $this->processVersion->process->author->licensedProcesses();

        // Everybody has access to allisa/person process
        $allisaPerson = Process::whereFullNamespace('allisa/person')->with(['latestPublishedVersion', 'author'])->first();

        if ($allisaPerson instanceof Process) {
            $processes->add($allisaPerson);
        }

        /** @noinspection PhpParamsInspection */
        $allProcesses = $processes->concat($licensedProcesses);

        // Die SupportData-Resource wird anhand des Templates ermittelt.
        $name = ucfirst(Str::camel($this->resource->template));
        $className = 'App\Http\Resources\\' . $name . 'SupportData';

        if (!class_exists($className)) {
            throw new Exception("Die Klasse {$name}SupportData existiert nicht!");
        }

        try {
            /** @var BaseSupportData $supportData */
            $supportData = new $className($allProcesses, $this->processVersion, $this->resource);
        }
        catch (Exception $exception) {
            report($exception);
        }

        // When parts are defined, we only return those keys.
        if (count($this->parts)) {
            return array_intersect_key($supportData->toArray($request), array_flip($this->parts));
        }

        return $supportData->toArray($request);
    }
}

<?php

namespace App\Http\Resources;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\ListConfig;
use App\ProcessType\Output;
use App\Traits\UsesAliasString;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class BaseSupportData
 * Gibt die Standard-Support-Daten für eine ListSupport-Resource zurück.
 * @package App\Http\Resources
 */
class BaseSupportData extends JsonResource {

    use UsesAliasString;

    /**
     * @var Collection All accessible processes
     */
    public $resource;

    public ProcessVersion $processVersion;
    public ListConfig $listConfig;

    /**
     * Create a new resource instance.
     * @param mixed $resource
     * @return void
     */
    public function __construct($resource, ProcessVersion $processVersion, ListConfig $listConfig) {
        $this->processVersion = $processVersion;
        $this->listConfig = $listConfig;

        parent::__construct($resource);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'allColumns' => $this->getCoreTableColumns(),
            'select' => $this->getSelectItems()
        ];
    }

    /**
     * Gibt die möglichen Tabellen zurück, die als FROM bei einer "custom"-Listenkonfiguration gewählt werden könnnen.
     */
    public function getTables() {
        return config('list_config.tables');
    }

    /**
     * Returns the select items of the list config in a [[ 'column' => 'foo', 'alias' => 'bar']].
     * Required to load the available select items for e.g. the Autocomplete Form field using a list.
     * @return array<array{ column: string, alias: string }>
     */
    public function getSelectItems(): array {
        $source = $this->listConfig->getSource();
        $items = [];

        foreach ($source['select'] ?? [] as $item) {
            if (is_string($item) && str_contains($item, ' as ')) {
                $items[] = [
                    'column' => explode(' as ', $item)[0],
                    'alias' => explode(' as ', $item)[1],
                ];
            }
            if (is_array($item) && array_key_exists('data', $item) && array_key_exists('alias', $item)) {
                $items[] = [
                    'column' => $item['data'],
                    'alias' => $item['alias']
                ];
            }
        }

        return $items;
    }

    /**
     * Gibt die statischen Tabellen-Spalten mit "column", "alias" und "label" zurück.
     * @param array $tables
     * @return array
     */
    public function getCoreTableColumns(array $tables = []): array {
        if (empty($tables)) {
            $columns = collect(config('list_config.columns'));
        }
        else {
            $columns = collect(config('list_config.columns'))->filter(fn($ele, $key) => in_array($key, $tables));
        }

        return [...$columns->flatten(1)->values()];
    }

    /**
     * Gibt Urls zu Prozess-Aktionen, Initial-Aktionen etc zurück.
     * @param Collection $processVersions
     * @param bool $latestVersions
     * @return array
     */
    public static function getProcessUrls(Collection $processVersions, bool $latestVersions = false): array {
        $items = [];

        /* @var ProcessVersion $processVersion */
        foreach ($processVersions as $processVersion) {
            $definition = $processVersion->definition;
            $version = $latestVersions ? 'latest' : $definition->version;
            $fullNamespace = $definition->namespace . '/' . $definition->identifier . '@' . $version;

            // Listen
            /* @var ListConfig $listConfig */
            foreach ($definition->listConfigs as $listConfig) {
                $key = 'list-' . $listConfig->slug;
                $items[$key] = [
                    'label' => 'Prozess-Liste - ' . $listConfig->slug,
                    'url' => '/processes/$/lists/' . $listConfig->slug,
                    'bindings' => ['']
                ];
            }

            // Aktionen
            /* @var ActionType $actionType */
            foreach ($definition->actionTypes as $actionType) {
                $key = 'action-' . $actionType->reference;
                $items[$key] = [
                    'label' => 'Aktion - ' . $actionType->reference,
                    'url' => '/processes/$/actiontypes/' . $actionType->reference,
                    'bindings' => ['']
                ];

                // API Aktion
                $key = 'action-api-' . $actionType->reference;
                $items[$key] = [
                    'label' => 'REST-API Aktion - ' . $actionType->reference,
                    'url' => '/api/v1/processes/$/actiontypes/' . $actionType->reference,
                    'bindings' => ['']
                ];

                // Initial-Aktionen
                $key = 'initial-' . Str::kebab($fullNamespace) . '-' . $actionType->reference;
                $items[$key] = [
                    'label' => 'Initiale Aktion - ' . $fullNamespace . ' - ' . $actionType->reference,
                    'url' => '/processtypes/' . $definition->namespace . '_' . $definition->identifier . '/' . $version . '/start/' . $actionType->reference,
                    'bindings' => []
                ];
            }

            // Artefakt-Urls
            /* @var Output $output */
            foreach ($definition->outputs as $output) {
                $key = 'output-' . $output->name;
                $items[$key] = [
                    'label' => 'Artefakt-Url - ' . $output->name,
                    'url' => '/processes/$/artifacts/' . $output->name,
                    'bindings' => ['']
                ];
            }
        }

        // Remove duplicate Urls.
        $collection = collect($items);
        $collection = $collection->unique('url');

        return $collection->toArray();
    }

    /**
     * Gibt die Alias/Spalten/Label Daten für eine Statustypen-Collection zurück.
     * @param Collection $statusTypes
     * @return array
     */
    protected function statusTypeSupportData(Collection $statusTypes): array {
        $items = [];

        foreach ($statusTypes as $statusType) {
            $alias = str_replace('-', '_', $statusType->reference);

            $items[] = [
                'column' => 'processes.situation->' . $statusType->reference . '->value::numeric',
                'alias' => 'status_' . $alias . '_value',
                'label' => 'Status - ' . $statusType->reference . ' - Wert'
            ];
            $items[] = [
                'column' => 'processes.situation->' . $statusType->reference . '->text_value',
                'alias' => 'status_' . $alias . '_text_value',
                'label' => 'Status - ' . $statusType->reference . ' - Text-Wert'
            ];
            $items[] = [
                'column' => 'processes.situation->' . $statusType->reference . '->color',
                'alias' => 'status_' . $alias . '_color',
                'label' => 'Status - ' . $statusType->reference . ' - Farbe'
            ];
            $items[] = [
                'column' => 'processes.situation->' . $statusType->reference . '->image',
                'alias' => 'status_' . $alias . '_image',
                'label' => 'Status - ' . $statusType->reference . ' - Icon'
            ];
        }

        return $items;
    }


    /**
     * Gibt Urls zu System-Bereichten zurück. Wird bei der Listenkonfiguration genutzt um vordefinierte Links zu setzen.
     * @return array
     */
    public function getSystemUrls(): array {
        return [];
    }

}

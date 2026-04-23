<?php

namespace App\Traits;

use App\Environment\Bot;
use App\Environment\Connector;
use App\Environment\Group;
use App\Environment\PublicApi;
use App\Environment\Task;
use App\Environment\User;
use App\Environment\Variable;
use App\Loaders\PipeLoader;
use App\Models\Environment;
use App\ProcessType\Definition;
use App\ProcessType\Processor;
use Illuminate\Support\Collection;

/**
 * Genutzte externe Prozesse, Aktionstyp-Plugins und Statustyp-Plugins ermitteln
 * und die Abhängigkeiten aktualiseren (Definition-Attribut: "dependencies").
 * Trait UpdatesDependencies
 * @package App\Traits
 */
trait UpdatesDependencies {

    /**
     * Genutzte externe Prozesse, Aktionstyp-Plugins und Statustyp-Plugins ermitteln
     * und die Abhängigkeiten aktualisieren (Definition-Attribut: "dependencies").
     * @param Definition $definition
     * @param Collection $environments
     * @return Definition
     */
    public static function updateDefinitionDependencies(Definition $definition, Collection $environments): Definition {
        $actionTypeComponentPluginDependencies = [];
        $statusTypePluginDependencies = [];
        $processTypeDependencies = [];
        $customProcessorPluginDependencies = [];

        // Check processors
        foreach ($definition->actionTypes as $actionType) {
            // External processes used by processors
            /* @var Processor $processor */
            foreach ($actionType->processors as $processor) {
                // Processor: Create process
                if ($processor->identifier === 'create_process' && ($processor->options['process_type'] ?? null)) {
                    $processTypeDependencies[] = $processor->options['process_type'];
                }

                // Processor: Execute action
                if ($processor->identifier === 'execute_action' && ($processor->options['action_type'] ?? null)) {
                    $processTypeDependencies[] = PipeLoader::getFullNamespaceWithVersion($processor->options['action_type']);
                }

                if (Definition::validNamespace($processor->identifier)) {
                    $customProcessorPluginDependencies[] = $processor->identifier;
                }
            }

            // Component plugins
            foreach ($actionType->components as $component) {
                $actionTypeComponentPluginDependencies[] = $component->getFullNamespace();
            }

            // Statustype plugins
            foreach ($definition->statusTypes as $statusType) {
                $statusTypePluginDependencies[] = $statusType->getFullNamespace();
            }
        }

        // Check new buttons of list configs for initial action urls
        // Check group aliases from list 'group_members'
        $listGroupAliases = [];

        foreach ($definition->listConfigs as $listConfig) {
            if ($url = $listConfig->data['header_button']['type_options']['url'] ?? '') {
                // e.g. /processtypes/robert_person/latest/start/3fe9d4df-a5ec-4ecc-b335-3aa5324bba09
                $url = ltrim($url, '\/'); // processtypes/robert_person/latest/start/3fe9d4df-a5ec-4ecc-b335-3aa5324bba09
                $parts = explode('/', $url);

                // Check if this url is an initial action url
                if ($parts[0] === 'processtypes' && $parts[3] === 'start') {
                    $fullnamespaceWithVersion = str_replace('_', '/', $parts[1]) . '@' . $parts[2];
                    $processTypeDependencies[] = $fullnamespaceWithVersion;
                }
            }

            if ($listConfig->data['source_type'] === 'accesses' && $listConfig->data['source']['type'] === 'group_accesses') {
                $groups = $listConfig->data['source']['items'];

                foreach ($groups as $group) {
                    $listGroupAliases[] = $group['name'];
                }
            }
        }

        // Check environments
        $groupAliases = collect($environments->reduce(function ($carry, Environment $environment) {
            return [
                ...$carry,
                ...($environment->blueprint->groups ?? collect())->map(fn(Group $group) => $group->aliases)->flatten()->toArray()
            ];
        }, $listGroupAliases))->unique();

        $userAliases = collect($environments->reduce(function ($carry, Environment $environment) {
            return [
                ...$carry,
                ...($environment->blueprint->users ?? collect())->map(fn(User $user) => $user->aliases)->flatten()->toArray()
            ];
        }, []))->unique()->filter(fn($item) => trim((string) $item) !== '');

        $botAliases = collect($environments->reduce(function ($carry, Environment $environment) {
            return [
                ...$carry,
                ...($environment->blueprint->bots ?? collect())->map(fn(Bot $bot) => $bot->aliases)->flatten()->toArray()
            ];
        }, []))->unique()->filter(fn($item) => trim((string) $item) !== '');

        $publicApiAliases = collect($environments->reduce(function ($carry, Environment $environment) {
            return [
                ...$carry,
                ...($environment->blueprint->publicApis ?? collect())->map(fn(PublicApi $publicApi) => $publicApi->slug)
            ];
        }, []))->unique();

        $variableIdentifiers = collect($environments->reduce(function ($carry, Environment $environment) {
            return [
                ...$carry,
                ...($environment->blueprint->variables ?? collect())->map(fn(Variable $variable) => $variable->identifier)
            ];
        }, []))->unique();

        $taskIdentifiers = collect($environments->reduce(function ($carry, Environment $environment) {
            return [
                ...$carry,
                ...($environment->blueprint->tasks ?? collect())->map(fn(Task $task) => $task->identifier)
            ];
        }, []))->unique();

        $connectorRequestIdentifiers = [];

        /* @var Environment $environment */
        foreach ($environments as $environment) {
            $blueprint = $environment->blueprint;

            /* @var Connector $connector */
            foreach ($blueprint->connectors as $connector) {
                $requests = $blueprint->requests->where('connector_id', '=', $connector->id)->pluck('identifier')->unique();

                // Nur wenn Requests nicht leer ist und der Connector-Identifier kein Demo-Identifier ist.
                if ($requests->isNotEmpty() && self::filterDemoAliases(collect($connector->identifier))->isNotEmpty()) {
                    $connectorRequestIdentifiers[$connector->identifier] = self::filterDemoAliases($requests)->toArray();
                }
            }
        }

        // In den URLs der Menüpunkte prüfen
        foreach ($definition->menuItems as $menuItem) {
            $urlsParts = explode('/', $menuItem->url ?? '');

            foreach ($urlsParts as $urlsPart) {
                $urlsPart = str_replace('_', '/', $urlsPart);
                if (Definition::validNamespace($urlsPart)) {
                    $processTypeDependencies[] = $urlsPart;
                }
            }
        }

        // Nach Demo-Aliases filtern
        $groupAliases = array_values(self::filterDemoAliases($groupAliases)->toArray());
        $userAliases = array_values(self::filterDemoAliases($userAliases)->toArray());
        $botAliases = array_values(self::filterDemoAliases($botAliases)->toArray());
        $variableIdentifiers = array_values(self::filterDemoAliases($variableIdentifiers)->toArray());
        $taskIdentifiers = array_values(self::filterDemoAliases($taskIdentifiers)->toArray());
        $publicApiAliases = array_values(self::filterDemoAliases($publicApiAliases)->toArray());

        $definition->dependencies = [
            'process_types' => array_filter(array_values(array_unique($processTypeDependencies)), fn($item) => $item !== $definition->getFullNamespace()),
            'action_type_component_plugins' => array_values(array_unique($actionTypeComponentPluginDependencies)),
            'status_type_plugins' => array_values(array_unique($statusTypePluginDependencies)),
            'custom_processor_plugins' => array_values(array_unique($customProcessorPluginDependencies)),
            'group_aliases' => $groupAliases,
            'user_aliases' => $userAliases,
            'bot_aliases' => $botAliases,
            'variable_identifiers' => $variableIdentifiers,
            'task_identifiers' => $taskIdentifiers,
            'public_api_identifiers' => $publicApiAliases,
            'connector_request_identifiers' => $connectorRequestIdentifiers
        ];

        return $definition;
    }


    /**
     * Entfernt Demo-Aliases dessen Resourcen nicht zu den Abhängigkeiten hinzugefügt werden.
     * Der Alias darf keinen der Demo-Namen haben und nicht mit "demo" beginnen.
     */
    public static function filterDemoAliases(Collection $aliases) {
        return $aliases->filter(fn($ele) => !str_starts_with($ele, 'demo_'));
    }
}

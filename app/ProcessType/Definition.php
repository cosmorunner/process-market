<?php /** @noinspection PhpDocFieldTypeMismatchInspection */

namespace App\ProcessType;

use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class Definition
 * @package App\ProcessType
 */
class Definition extends AbstractModel {

    const EMPTY_DEPENDENCIES = [
        'process_types' => [],
        'action_type_component_plugins' => [],
        'status_type_plugins' => [],
        'custom_processor_plugins' => [],
        'group_aliases' => [],
        'user_aliases' => [],
        'bot_aliases' => [],
        'variable_identifiers' => [],
        'public_api_identifiers' => [],
        'connector_request_identifiers' => [],
    ];

    public string $name;
    public string $description;
    public string $namespace;
    public string $identifier;
    public string $image;
    public string $version;
    public string $definition_version;
    public string $reference_pattern;
    public array $dependencies = [];
    public array $javascript = [];
    public array $unique = [];
    public ?string $default_role_id = null;
    public ?string $public_role_id = null;
    public ?string $history_list_config_slug = null;
    public ?string $published_at = null;
    public ?Role $defaultRole;
    public ?Role $publicRole;
    public ?ListConfig $historyListConfig;

    /**
     * @var Collection|Output[]
     */
    public Collection $outputs;

    /**
     * @var Collection|StatusType[]
     */
    public Collection $statusTypes;

    /**
     * @var Collection|ActionType[]
     */
    public Collection $actionTypes;

    /**
     * @var Collection|State[]
     */
    public Collection $states;

    /**
     * @var Collection|Role[]
     */
    public Collection $roles;

    /**
     * @var Collection|Event[]
     */
    public Collection $events;

    /**
     * @var Collection|Listener[]
     */
    public Collection $listeners;

    /**
     * @var Collection|ListConfig[]
     */
    public Collection $listConfigs;

    /**
     * @var Collection|MenuItem[]
     */
    public Collection $menuItems;

    /**
     * @var Collection|Template[]
     */
    public Collection $templates;

    /**
     * @var Collection|RelationType[]
     */
    public Collection $relationTypes;

    /**
     * @var Collection|Category[]
     */
    public Collection $categories;

    /**
     * Eigenschaften die eine Collection repräsentieren.
     * @var array
     */
    public $collections = [
        'outputs' => Output::class,
        'statusTypes' => StatusType::class,
        'actionTypes' => ActionType::class,
        'menuItems' => MenuItem::class,
        'listConfigs' => ListConfig::class,
        'relationTypes' => RelationType::class,
        'categories' => Category::class,
        'templates' => Template::class,
        'roles' => Role::class,
        'events' => Event::class,
        'listeners' => Listener::class
    ];

    public function __construct(array $properties) {
        parent::__construct($properties);
        $this->afterConstruct();

        $this->defaultRole = $this->roles->firstWhere('id', '=', $this->default_role_id);
        $this->publicRole = $this->roles->firstWhere('id', '=', $this->public_role_id);
        $this->historyListConfig = $this->listConfigs->firstWhere('slug', '=', $this->history_list_config_slug);
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'namespace' => $this->namespace,
            'identifier' => $this->identifier,
            'image' => $this->image,
            'version' => $this->version,
            'definition_version' => $this->definition_version,
            'reference_pattern' => $this->reference_pattern,
            'default_role_id' => $this->default_role_id,
            'public_role_id' => $this->public_role_id,
            'history_list_config_slug' => $this->history_list_config_slug,
            'published_at' => $this->published_at,
            'outputs' => $this->outputs->map(fn(Output $output) => $output->toArray())->toArray(),
            'status_types' => $this->statusTypes->map(fn(StatusType $statusType) => $statusType->toArray())->toArray(),
            'action_types' => $this->actionTypes->map(fn(ActionType $actionType) => $actionType->toArray())->toArray(),
            'roles' => $this->roles->map(fn(Role $role) => $role->toArray())->toArray(),
            'events' => $this->events->map(fn(Event $event) => $event->toArray())->toArray(),
            'list_configs' => $this->listConfigs->map(fn(ListConfig $listConfig) => $listConfig->toArray())->toArray(),
            'menu_items' => $this->menuItems->map(fn(MenuItem $menuItem) => $menuItem->toArray())->toArray(),
            'templates' => $this->templates->map(fn(Template $template) => $template->toArray())->toArray(),
            'relation_types' => $this->relationTypes->map(fn(RelationType $relationType) => $relationType->toArray())->toArray(),
            'categories' => $this->categories->map(fn(Category $category) => $category->toArray())->toArray(),
            'listeners' => $this->listeners->map(fn(Listener $listener) => $listener->toArray())->toArray(),
            'dependencies' => $this->dependencies,
            'javascript' => $this->javascript,
            'unique' => $this->unique
        ];
    }

    /**
     * Prüft ob ein String einem gültigen Prozesstyp-Identifier entspricht.
     * Ein Identifier besteht aus Namespace, Identifier und Verion, z.B. allisa/demo@1.0.0
     * @param string $identifier
     * @return bool
     */
    public static function validNamespace($identifier) {
        if (!is_string($identifier)) {
            return false;
        }

        $namespace = explode('@', $identifier)[0] ?? '';
        $version = explode('@', $identifier)[1] ?? '';

        // z.B. allisa/demo@1.0.0 oder alinea-technology/requirement-report@1.3.7 oder allisa/demo@latest
        $regexNamespace = "/^([\da-z-]){2,}\/([\da-z-]){2,}$/";
        $regexVersion = "/^(\d+\.)+(\d+\.)+(\*|\d+)$/";

        $validNamespace = (bool) preg_match($regexNamespace, $namespace);
        $validVersion = preg_match($regexVersion, $version) || $version === 'latest' || $version === 'develop';

        return $validNamespace && $validVersion;
    }

    /**
     * @return string
     */
    public function fullNamespace() {
        return $this->namespace . '/' . $this->identifier;
    }

    /**
     * Gibt den gesamten Namespace zurück.
     * @return string
     */
    public function fullNamespaceWithVersion() {
        return $this->fullNamespace() . '@' . $this->version;
    }

    /**
     * @param $id
     * @return Role|null
     */
    public function role($id) {
        return $this->roles->firstWhere('id', '=', $id);
    }

    /**
     * @param $id
     * @return Template|null
     */
    public function template($id) {
        return $this->templates->firstWhere('id', '=', $id);
    }

    /**
     * @param $id
     * @return Category|null
     */
    public function category($id) {
        return $this->categories->firstWhere('id', '=', $id);
    }

    /**
     * @param $id
     * @return RelationType|null
     */
    public function relationType($id) {
        return $this->relationTypes->firstWhere('id', '=', $id);
    }

    /**
     * @param $identifier
     * @return ListConfig|null
     */
    public function listConfig($identifier) {
        if (Uuid::isValid($identifier)) {
            return $this->listConfigs->firstWhere('id', '=', $identifier);
        }

        return $this->listConfigs->firstWhere('slug', '=', $identifier);
    }

    /**
     * @param $id
     * @return MenuItem|null
     * @noinspection PhpUnused
     */
    public function menuItem($id) {
        return $this->menuItems->firstWhere('id', '=', $id);
    }

    /**
     * @param $identifier
     * @return ActionType|null
     */
    public function actionType($identifier) {
        if (is_string($identifier) && Uuid::isValid($identifier)) {
            return $this->actionTypes->firstWhere('id', '=', $identifier);
        }

        return $this->actionTypes->firstWhere('reference', '=', $identifier);
    }

    /**
     * @param $id
     * @return Listener|null
     */
    public function listener($id) {
        return $this->listeners->firstWhere('id', '=', $id);
    }

    /**
     * @param $id
     * @return Event|null
     */
    public function event($id) {
        return $this->events->firstWhere('id', '=', $id);
    }

    /**
     * @param $name
     * @return Event|null
     * @noinspection PhpUnused
     */
    public function eventByName($name) {
        return $this->events->firstWhere('name', '=', $name);
    }

    /**
     * @param $id
     * @return Output|null
     */
    public function output($id) {
        return $this->outputs->firstWhere('id', '=', $id);
    }

    /**
     * @param string $name
     * @return null|Output
     */
    public function outputByName(string $name) {
        return $this->outputs->firstWhere('name', '=', $name);
    }

    /**
     * @param $identifier
     * @return StatusType|null
     */
    public function statusType($identifier) {
        if (Uuid::isValid($identifier)) {
            return $this->statusTypes->firstWhere('id', '=', $identifier);
        }
        else {
            return $this->statusTypes->firstWhere('reference', '=', $identifier);
        }
    }

    /**
     * Gibt den ganzen Namespace mit Version von der Definition zurück.
     * @return string
     */
    public function getFullNamespace() {
        return $this->namespace . '/' . $this->identifier . '@' . $this->version;
    }

    /**
     * Gibt die Namespaces der Prozess-Abhängigkeiten zurück.
     * @return string[]
     */
    public function processTypeDependencies() {
        return $this->dependencies['process_types'] ?? [];
    }

    /**
     * @param $statusTypeId
     * @param $stateId
     * @return State|null
     */
    public function state($statusTypeId, $stateId) {
        $statusType = $this->statusTypes->firstWhere('id', '=', $statusTypeId);

        return $statusType->states->firstWhere('id', '=', $stateId);
    }
}

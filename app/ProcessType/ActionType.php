<?php /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */

/** @noinspection PhpDocFieldTypeMismatchInspection */

namespace App\ProcessType;

use App\Enums\ProcessRolePermissions;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Class ActionType
 * @package App\ProcessType
 */
class ActionType extends AbstractModel {

    public string $id;
    public string $name;
    public string $reference;
    public ?string $description;
    public ?string $category_id;
    public ?Category $category;
    public ?string $image;
    public int $sort;
    public bool $instant = false;
    public bool $full_width = false;
    public bool $show_save_button = true;
    public string $save_label = 'app.execute';
    public array $javascript = [];
    public Definition $definition;

    /**
     * @param Definition $definition
     */
    protected function setup($definition) {
        $this->definition = $definition;
    }

    /**
     * Aktionsdaten-Konfiguration, mit denen bestimmt wird, welche Daten für eine Aktion vorgeladen werden.
     * @var Collection|Input[]
     */
    public Collection $inputs;

    /**
     * Aktionsdaten-Konfiguration, mit denen bestimmt wird, welche Daten der Aktion in den Aktionsdaten gespeichert werden.
     * @var Collection|Output[]
     */
    public Collection $outputs;

    /**
     * Prozessoren des Aktionstyps. Werden durch den App\Executors\ProcessorsExecutor verarbeitet.
     * @var Collection|Processor[]
     */
    public Collection $processors;

    /**
     * @var Collection|ActionRule[]
     */
    public Collection $actionRules;

    /**
     * @var Collection|StatusRule[]
     */
    public Collection $statusRules;

    /**
     * @var Collection|ActionTypeComponent[]
     */
    public Collection $components;

    protected $casts = [
        'sort' => 'integer'
    ];

    protected $collections = [
        'inputs' => Input::class,
        'outputs' => Output::class,
        'actionRules' => ActionRule::class,
        'statusRules' => StatusRule::class,
        'processors' => Processor::class,
        'components' => ActionTypeComponent::class
    ];

    /**
     * Prozessor anhand einer Id zurückgeben.
     * @param string $id
     * @return Processor|null
     */
    public function processor(string $id) {
        return $this->processors->firstWhere('id', '=', $id);
    }

    /**
     * Gibt mögliche Werte eines Feldes zurück.
     * Primär wird der Output-Validator auf die Regel "in" geprüft.
     * Beispielsweise die möglichen Radio-Optionen bei einem Radio-Feld.
     * Bei Feldern wo die möglichen Werte nicht eindeutig bestimmbar
     * sind (Text, Textarea, Hidden) oder nicht vorhandenem Feld wird null zurückgegeben.
     * @param string $fieldName
     * @return array
     */
    public function getPossibleValues(string $fieldName) {
        $output = $this->output($fieldName);

        // Prüfen ob es eine "in" Regel beim Output gibt.
        if ($output && $output->hasValidationRule('in')) {
            $rule = $output->getValidationRule('in');
            $valueString = explode(':', $rule)[1] ?? '';
            $values = explode(',', $valueString);

            return array_filter(array_map('trim', $values), fn($value) => is_numeric($value));
        }

        // Sekundär wird auf ein Formularfeld geprüft.
        $field = $this->getFieldByName($fieldName);

        if (is_null($field)) {
            return [];
        }

        $type = $field['type'];

        if ($type === 'checkbox') {
            return [$field['checked'], $field['unchecked']];
        }

        if ($type === 'radio' || $type === 'select') {
            $items = $field['items'] ?? [];

            return collect($items)->map(function ($item) {
                return $item['value'];
            })->filter(function ($value) {
                return $value !== '' && is_numeric($value);
            })->toArray();
        }

        return [];
    }

    /**
     * Gibt ein Formularfeld anhand eines Namen zurück.
     * @param string $name
     * @return array
     */
    public function getFieldByName(string $name) {
        return $this->getFields()->first(function ($field) use ($name) {
            return $field['name'] === $name;
        });
    }


    /**
     * Setzt das "fields"-Property
     * @return Collection
     */
    private function getFields() {
        $fieldsCollection = collect();

        // Nur Formulare
        $this->components->filter(function (ActionTypeComponent $component) {
            return $component->namespace === 'allisa' && $component->identifier === 'form';
        })->each(function (ActionTypeComponent $component) use ($fieldsCollection) {
            $sets = collect($component->options)->get('sets', collect());

            if (is_array($sets)) {
                $sets = collect($sets);
            }

            $sets->each(function ($set) use ($fieldsCollection) {
                $fields = $set['fields'] ?? [];

                foreach ($fields as $field) {
                    $fieldsCollection->add($field);
                }
            });
        });

        return $fieldsCollection;
    }

    /**
     * Gibt eine Aktionsregel anhand einer StatusTyp-Id zurück.
     * @param string $statusTypeId
     * @return ActionRule|null
     */
    public function actionRule(string $statusTypeId) {
        return $this->actionRules->firstWhere('status_type_id', '=', $statusTypeId);
    }

    /**
     * Gibt eine Statusregel anhand einer StatusTyp-Id zurück.
     * @param string $statusTypeId
     * @return StatusRule|null
     */
    public function statusRule(string $statusTypeId) {
        return $this->statusRules->firstWhere('status_type_id', '=', $statusTypeId);
    }

    /**
     * Gibt eine Komponente anhand einer Id zurück.
     * @param string $id
     * @return ActionTypeComponent|null
     */
    public function component(string $id) {
        return $this->components->firstWhere('id', '=', $id);
    }

    /**
     * Gibt einen Input zurück.
     * @param string $name
     * @return Input|null
     */
    public function input(string $name) {
        return $this->inputs->firstWhere('name', '=', $name);
    }

    /**
     * Gibt einen Output zurück.
     * @param string $name
     * @return Output|null
     */
    public function output(string $name) {
        return $this->outputs->firstWhere('name', '=', $name);
    }

    /**
     * Fügt eine leere Allisa/Form Komponente hinzu.
     */
    public function addFormComponent() {
        $this->components->add(ActionTypeComponent::make([
            'label' => '',
            'namespace' => 'allisa',
            'action_type_id' => $this->id,
            'sort' => 0,
            'identifier' => 'form',
            'options' => [
                'width' => 12,
                'sets' => [
                    [
                        'label' => '',
                        'sort' => 0,
                        'width' => '12',
                        'fields' => []
                    ]
                ]
            ]
        ]));
    }

    /**
     * Prüft ob ein Formular-Feld mit dem Namen existiert.
     * @param string $name
     * @return bool
     */
    public function formFieldExists(string $name) {
        $fields = $this->formfields();

        return (bool) collect($fields)->firstWhere('name', '=', $name);
    }

    /**
     * Gibt alle Allisa/Form Components zurück.
     * @return Collection
     */
    public function formComponents() {
        return $this->components->filter(function (ActionTypeComponent $component) {
            return $component->namespace === 'allisa' && $component->identifier === 'form';
        });
    }

    /**
     * Gibt alle Formularfelder vom Typ Allisa/Form zurück.
     * @return array
     */
    public function formfields() {
        $fields = [];
        $this->formComponents()->each(function (ActionTypeComponent $component) use (&$fields) {
            $fields = [...$fields, ...$component->getFields()];
        });

        return $fields;
    }

    /**
     * Erzeugt basierend auf den Regeln ein passendes Formular-Feld. So wird beispielsweise ein Select-Feld erzeugt,
     * wenn die "in_array"-Regel genutzt wird.
     * @param $name
     * @param $rules
     */
    public function createFormfieldFromValidationRules($name, $rules) {
        $options = [
            'type' => 'text',
            'name' => $name,
            'label' => Str::title(Str::replace('_', ' ', $name)),
            'helper_text' => '',
            'default' => '',
            'width' => 12,
        ];

        // Select-Feld
        if ($inArrayRule = collect($rules)->first(fn($item) => Str::startsWith($item, 'in:'))) {
            $options['type'] = 'select';
            $values = substr($inArrayRule, strlen('in:'));
            $values = explode(',', $values);
            $items = [
                [
                    'label' => 'Bitte wählen...',
                    'value' => ''
                ]
            ];

            $other = array_map(function ($option) {
                return [
                    'label' => $option,
                    'value' => $option
                ];
            }, $values);

            foreach ($other as $otherItem) {
                $items[] = $otherItem;
            }

            $options['items'] = $items;

        }

        $this->addFieldToFormComponent($options);
    }

    /**
     * Fügt ein Formular-Feld zu einer Allisa/Form Componente hinzu. Falls keine Allisa/Form Componente existiert
     * wird eine erzeugt.
     * @param $field
     */
    public function addFieldToFormComponent($field) {
        if ($this->formComponents()->isEmpty()) {
            $this->addFormComponent();
        }

        /* @var ActionTypeComponent $formComponent */
        $formComponent = $this->formComponents()->first();
        $formComponent->addField($field);
    }

    /**
     * Erzeugt ein neues ActionType-Objekt mit Standardwerten
     * @param array $options
     * @return ActionType
     */
    public static function make(array $options = []) {
        $id = array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()
            ->toString();
        $name = $options['name'] ?? 'Neue Aktion';
        $randomReference = Str::limit(Str::slug($name, '_'), 16, '') . '_' . strtolower(Str::random(4));

        return new self([
            'id' => $id,
            'category_id' => $options['category_id'] ?? Uuid::uuid4()->toString(),
            'name' => $name,
            'reference' => empty($options['reference']) ? $randomReference : $options['reference'],
            'description' => $options['description'] ?? '',
            'image' => $options['image'] ?? '',
            'instant' => $options['instant'] ?? false,
            'full_width' => $options['full_width'] ?? false,
            'show_save_button' => $options['show_save_button'] ?? true,
            'sort' => $options['sort'] ?? 0,
            'inputs' => $options['inputs'] ?? [],
            'outputs' => $options['outputs'] ?? [],
            'action_rules' => $options['action_rules'] ?? [],
            'status_rules' => $options['status_rules'] ?? [],
            'processors' => $options['processors'] ?? [],
            'javascript' => $options['javascript'] ?? [],
            'components' => $options['components'] ?? [],
            'save_label' => $options['save_label'] ?? 'app.execute'
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'reference' => $this->reference,
            'description' => $this->description,
            'image' => $this->image,
            'sort' => $this->sort,
            'instant' => $this->instant,
            'full_width' => $this->full_width,
            'show_save_button' => $this->show_save_button,
            'inputs' => $this->inputs->map(fn(Input $input) => $input->toArray())->toArray(),
            'outputs' => $this->outputs->map(fn(Output $output) => $output->toArray())->toArray(),
            'action_rules' => $this->actionRules->map(fn(ActionRule $actionRule) => $actionRule->toArray())->toArray(),
            'status_rules' => $this->statusRules->map(fn(StatusRule $statusRule) => $statusRule->toArray())->toArray(),
            'processors' => $this->processors->map(fn(Processor $processor) => $processor->toArray())->toArray(),
            'components' => $this->components->map(fn(ActionTypeComponent $component) => $component->toArray())->toArray(),
            'javascript' => $this->javascript,
            'save_label' => $this->save_label
        ];
    }

    /**
     * Flagge ob die Prozessoren manuell sortiert wurden oder ob sie automatisch sortiert werden.
     */
    public function usesManualProcessorSorting() {
        return $this->processors->reduce(fn($carry, Processor $processor) => $carry && $processor->sort !== null, false);
    }

    /**
     * Returns all role names with permission to execute the given action
     * @return array
     */
    public function executableByRoles() {
        $rolesWithPermission = [];

        foreach ($this->definition->roles as $role) {
            if ($role->can(ident(ProcessRolePermissions::ExecuteActions->value)) || $role->can(ident(ProcessRolePermissions::ExecuteActiontype->value, $this->id))) {
                $rolesWithPermission[] = $role->name;
            }
        }

        return $rolesWithPermission;
    }
}


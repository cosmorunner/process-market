<?php

namespace App\ProcessType;

use App\Interfaces\Iconable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Class ActionTypeComponent
 * @package App\ProcessType
 */
class ActionTypeComponent extends AbstractModel implements Iconable {

    public string $id;
    public ?string $label;
    public ?string $css_classes = '';
    public string $namespace;
    public string $action_type_id;
    public int $sort;
    public string $identifier;
    public string $version;
    public string $width;
    public array $options = [];
    protected $casts = [
        'options' => 'array'
    ];

    /**
     * @inheritDoc
     */
    public static function icon(): string {
        return 'settings_input_component';
    }

    /**
     * Erzeugt ein neues Component-Objekt mit Standardwerten.
     * @param array $options
     * @return ActionTypeComponent
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()
                ->toString(),
            'label' => $options['label'] ?? '',
            'namespace' => $options['namespace'] ?? 'allisa',
            'css_classes' => $options['css_classes'] ?? '',
            'action_type_id' => $options['action_type_id'] ?? '',
            'sort' => $options['sort'] ?? 0,
            'identifier' => $options['identifier'] ?? 'form',
            'version' => $options['version'] ?? '1.0.0',
            'width' => $options['width'] ?? '12',
            'options' => $options['options'] ?? []
        ]);
    }

    /**
     * Fügt ein Feld zu einer Allisa/Form Componente hinzu, falls ein Feld mit dem Namen noch nicht existiert.
     * @param $field
     */
    public function addField($field) {
        if ($this->namespace !== 'allisa' || $this->identifier !== 'form') {
            return;
        }

        $sets = $this->options['sets'] ?? [];

        if (empty($sets)) {
            $this->options['sets'] = [
                'label' => '',
                'sort' => 0,
                'width' => 12,
                'fields' => [$field]
            ];
        }
        else {
            $fields = $this->options['sets'][count($sets) - 1]['fields'];
            $this->options['sets'][count($sets) - 1]['fields'] = [...$fields, $field];
        }
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'css_classes' => $this->css_classes,
            'namespace' => $this->namespace,
            'action_type_id' => $this->action_type_id,
            'sort' => $this->sort,
            'identifier' => $this->identifier,
            'version' => $this->version,
            'width' => $this->width,
            'options' => $this->options,
        ];
    }

    /**
     * Gibt alle Felder von einer Allisa/Form Componente hinzu.
     * @return array
     */
    public function getFields() {
        if ($this->namespace !== 'allisa' || $this->identifier !== 'form') {
            return [];
        }

        $fields = [];
        $sets = $this->options['sets'] ?? [];

        foreach ($sets as $set) {
            $fields = [...$fields, ...($set['fields'] ?? [])];
        }

        return $fields;
    }

    /**
     * Gibt den ganzen Namespace mit Version von der Komponente zurück.
     * @return string
     */
    public function getFullNamespace() {
        return $this->namespace . '/' . $this->identifier . '@' . $this->version;
    }

    /**
     * Pfad zur Meta-Datei des Component-Plugins.
     */
    private function metaFileName() {
        $version = 'v' . str_replace('.', '_', $this->version);
        $namespace = ucfirst(Str::camel($this->namespace));
        $identifier = ucfirst(Str::camel($this->identifier));

        return plugins_path($namespace) . '/' . $namespace . '/ActionTypeComponent/' . $identifier . '/' . $version . '/configuration/Meta.php';
    }

    /**
     * Gibt den Namen der Meta-Klasse zurück, in der die u.a. die Validierungsregeln für
     * das "options"-Attribut der Komponente hinterlegt wird.
     * @return string
     */
    private function metaClassName() {
        $version = 'v' . str_replace('.', '_', $this->version);
        $namespace = ucfirst(Str::camel($this->namespace));
        $identifier = ucfirst(Str::camel($this->identifier));

        if ($this->namespace === config('app.plugins.internal_namespace')) {
            $config = config('app.plugins.namespace.internal');
        }
        else {
            $config = config('app.plugins.namespace.external');
        }

        return $config . '\\' . $namespace . '\\ActionTypeComponent\\' . $identifier . '\\' . $version . '\\configuration\\Meta';
    }

    /**
     * Gibt die Plugin-Datei "Meta.php" aus dem "configuration"-Ordner zurück.
     */
    public function getPluginMetaClass() {
        $metaFilename = $this->metaFileName();

        if (!File::exists($metaFilename)) {
            return null;
        }

        include $metaFilename;

        $class = $this->metaClassName();

        if (class_exists($class)) {
            return new $class($this);
        }

        return null;
    }

}


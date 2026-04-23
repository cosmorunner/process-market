<?php /** @noinspection PhpUnused */

namespace App\ProcessType;

use Ramsey\Uuid\Uuid;

/**
 * Class ListConfig
 * @package App\ProcessType
 */
class ListConfig extends AbstractModel {

    const SOURCE_SQL = 'sql';
    const SOURCE_RELATION = 'relation';
    const SOURCE_CONNECTOR_REQUEST = 'connector_request';
    const SOURCE_REFERENCE = 'reference';
    const SOURCE_ACCESSES = 'accesses';

    public string $id;
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $template;
    public array $roles = [];
    public array $data = [];
    public Definition $definition;

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Erzeugt ein neues ListConfig-Object mit Standardwerten.
     * @param array $options
     * @return ListConfig
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'name' => $options['name'] ?? 'Liste ' . rand(1, 1000),
            'slug' => (string)($options['slug'] ?? 'list-slug-' . rand(1, 1000)),
            'description' => $options['description'] ?? '',
            'template' => $options['template'] ?? 'processes',
            'data' => $options['data'] ?? []
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'template' => $this->template,
            'data' => $this->data,
            'data_aliases' => $this->getDataAliases(),
            'custom_data_names' => $this->getCustomDataNames()
        ];
    }

    /**
     * Gibt je nach Data Source-Typ die genutzten Data-Aliases des "selects" oder "mapping" zurück.
     * Wird genutzt um bei einer Collection-Aktionstyp-Komponente das Mapping zu ermöglichen.
     * @return array
     */
    public function getDataAliases() {
        switch ($this->data['source_type'] ?? null) {
            case self::SOURCE_SQL:
                $select = $this->data['source']['select'] ?? [];

                return collect($select)->map(fn($ele) => explode(' as ', $ele)[1] ?? '')
                    ->filter(fn($ele) => $ele !== '')
                    ->toArray();
            case self::SOURCE_RELATION:
            case self::SOURCE_CONNECTOR_REQUEST:
                $select = $this->data['source']['select'] ?? [];

                return collect($select)->map(fn($ele) => $ele['alias'] ?? '')
                    ->filter(fn($ele) => $ele !== '')
                    ->toArray();
            default:
                return [];
        }
    }

    /**
     * Bei Input-Spalten (Text-Input, Select) kann ein eigener Daten-Name angegeben werden um diesen Namen als Key beim Mappen
     * auf Aktions-Daten zu nutzen (ohne dass die Spalte einen Wert hat).
     * @return array
     */
    public function getCustomDataNames(): array {
        $names = [];
        $columns = $this->data['columns'] ?? [];

        foreach ($columns as $column) {
            $customDataName = $column['type_options']['custom_data_name'] ?? null;

            if ($customDataName) {
                $names[] = $customDataName;
            }
        }

        return $names;
    }

    /**
     * Returns the source of the list config
     * @return array
     */
    public function getSource(): array {
        return $this->data['source'] ?? [];
    }

}

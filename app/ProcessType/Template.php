<?php

namespace App\ProcessType;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Stevebauman\Purify\Facades\Purify;

/**
 * Class Template
 * @package App\ProcessType
 */
class Template extends AbstractModel {

    const TWIG_ECHO_REGEX = '/\{\{\s*(.*?)\s*}}/';
    const TWIG_DIRECTIVE_REGEX = '/\{%\s*(.*?)\s*%}/';
    const TYPE_HTML = 'html';
    const TYPE_CUSTOM_LOGIC = 'custom_logic';
    const TYPE_MUSTACHE_LIST_COLUMN = 'mustache_list_column';

    const MAPPING_TYPE_STRING = 'string';
    const MAPPING_TYPE_ARRAY = 'array';
    const MAPPING_TYPE_LIST_CONFING = 'ListConfig';
    const MAPPING_TYPE_USER = 'User';
    const MAPPING_TYPE_GROUP = 'Group';
    const MAPPING_TYPE_JS = 'js';

    // Default values for template preview dataset values.
    const MAPPING_TYPE_STRING_DEFAULT_VALUE = '';
    const MAPPING_TYPE_ARRAY_DEFAULT_VALUE = ['Wert 1', 'Wert 2'];
    const MAPPING_TYPE_LIST_CONFIG_DEFAULT_VALUE = [
        [
            'listenzeile_1_alias_1' => 'Wert 1',
            'listenzeile_1_alias_2' => 'Wert 2',
        ],
        [
            'listenzeile_2_alias_1' => 'Wert 1',
            'listenzeile_2_alias_2' => 'Wert 2',
        ]
    ];

    const MAPPING_TYPE_USER_DEFAULT_VALUE = '[[faker.user]]';
    const MAPPING_TYPE_GROUP_DEFAULT_VALUE = '[[faker.group]]';

    const GLOBAL_VARIABLES = [
        'app_name',
        'app_description',
        'app_url',
        'app_image_url',
        'process_name',
        'process_id',
        'process_url',
        'process_data',
        'process_situation',
        'action_type_name',
        'time_24',
        'date_ddmmyyyy',
        'user_full_name',
        'user_first_name',
        'user_last_name',
        'user_email'
    ];

    public string $id;
    public string $name;
    public string $data;
    public string $type;
    public array $mapping = [];
    public array $stylesheets = [];
    public Definition $definition;

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Erzeugt ein neues Template-Object mit Standardwerten.
     * @param array $options
     * @return Template
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()
                ->toString(),
            'name' => $options['name'] ?? 'Neue Vorlage',
            'type' => $options['type'] ?? self::TYPE_HTML,
            'data' => $options['data'] ?? '',
            'mapping' => $options['mapping'] ?? [],
            'stylesheets' => $options['stylesheets'] ?? []
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'data' => $this->data,
            'type' => $this->type,
            'mapping' => $this->mapping,
            'stylesheets' => $this->stylesheets
        ];
    }

    /**
     * Entfernt unerlaubtes HTML und JS zu Sicherheitszecken und behält dabei Twig Tags.
     * Nur für Twig templates (HTML) geeignet.
     * @param string $html
     * @return array|string
     */
    public static function purifyHtml(string $html) {
        // Twig {{ ... }} Direktive durch UUIDs ersetzen, sodass die
        // Purify::clean()-Methode (htmlpurifier) diese nicht entfernt.
        preg_match_all(self::TWIG_ECHO_REGEX, $html, $matches);
        $echoReplaceMap = collect($matches[0])->mapWithKeys(fn($item, $key) => [Uuid::uuid4()->toString() => $item])->toArray();
        $echoReplacedHtml = str_replace(array_values($echoReplaceMap), array_keys($echoReplaceMap), $html);

        // Twig {% ... %} Direktive durch UUIDs ersetzen, sodass die "Purify" Klasse diese nicht entfernt.
        preg_match_all(self::TWIG_DIRECTIVE_REGEX, $html, $matches);
        $directiveReplaceMap = collect($matches[0])
            ->mapWithKeys(fn($item, $key) => [Uuid::uuid4()->toString() => $item])
            ->toArray();
        $directiveReplacedHtml = str_replace(array_values($directiveReplaceMap), array_keys($directiveReplaceMap), $echoReplacedHtml);

        $replaceMap = array_merge($echoReplaceMap, $directiveReplaceMap);

        // HTML säubern
        $purified = Purify::clean($directiveReplacedHtml);

        // UUIDs wieder ersetzen
        return str_replace(array_keys($replaceMap), array_values($replaceMap), $purified);
    }

    /**
     * Based on the type of a mapping value, this method returns the corresponding default preview dataset value.
     * @return array[]|string|string[]
     */
    public static function defaultPreviewDatasetValueBasedOnMappingType(string $type): array|string {
        return match ($type) {
            self::MAPPING_TYPE_STRING => self::MAPPING_TYPE_STRING_DEFAULT_VALUE,
            self::MAPPING_TYPE_ARRAY => self::MAPPING_TYPE_ARRAY_DEFAULT_VALUE,
            self::MAPPING_TYPE_LIST_CONFING => self::MAPPING_TYPE_LIST_CONFIG_DEFAULT_VALUE,
            self::MAPPING_TYPE_USER => self::MAPPING_TYPE_USER_DEFAULT_VALUE,
            self::MAPPING_TYPE_GROUP => self::MAPPING_TYPE_GROUP_DEFAULT_VALUE,
            default => ''
        };
    }

    /**
     * Returns the default dataset with values according to the mapping of the template.
     * @return array
     */
    public function defaultPreviewDataset() {
        $defaultValues = collect($this->mapping)
            ->mapWithKeys(fn($item, $name) => [$name => self::defaultPreviewDatasetValueBasedOnMappingType($item['type'])])
            ->toArray();

        return [
            'id' => Str::uuid()->toString(),
            'name' => 'Standard Vorschau-Werte',
            'values' => $defaultValues
        ];
    }
}

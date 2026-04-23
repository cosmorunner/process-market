<?php

namespace Database\Builder\Definition;

use App\ProcessType\ListConfig;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class ListConfigBuilder
 * @package Database\Builder\Definition
 */
class ListConfigBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'Default List',
            'slug' => 'all',
            'description' => 'Alle Prozesse.',
            'template' => 'processes',
            'data' => [
                'limit_to_user_involvement' => false,
                'rows_per_page' => 10,
                'header_button' => [
                    'label' => 'app.new_process',
                    'type' => 'link',
                    'type_options' => [
                        'url' => '/processes/create',
                        'bindings' => [],
                        'target' => '_self',
                    ]
                ],
                'enable_search' => true,
                'enable_pagination' => true,
                'enable_total_count' => true,
                'enable_download' => false,
                'quick_filter' => [],
                'source_type' => 'sql',
                'source' => [
                    'from' => 'processes',
                    'select' => [
                        'processes.id as processes_id',
                        'processes.name as processes_name',
                        'processes.image as processes_image',
                        'processes.created_at as processes_created_at',
                        'process_type_metas.name as process_type_metas_name',
                        'process_type_metas.image as process_type_metas_image',
                        'process_types.version as process_types_version'
                    ],
                    'join' => [
                        [
                            'process_type_metas',
                            'process_type_metas.id',
                            '=',
                            'processes.process_type_meta_id'
                        ],
                        [
                            'process_types',
                            'process_types.id',
                            '=',
                            'processes.process_type_id'
                        ]
                    ],
                    'orderBy' => [
                        [
                            'processes.created_at',
                            'desc'
                        ]
                    ],
                ],
                'columns' => [
                    'processes_image' => [
                        'type' => 'icon',
                        'alias' => 'process_type_metas_image',
                        'type_options' => [
                            'mapping' => [],
                            'colors' => [
                                'secondary' => ['*'],
                                'warning' => ['bug_report'],
                                'primary' => ['star'],
                            ]
                        ],
                        'label' => '',
                        'width' => 1,
                        'searchable' => false,
                        'sortable' => false,
                        'color' => ''
                    ],
                    'processes_name' => [
                        'type' => 'url',
                        'type_options' => [
                            'url' => '/processes/?',
                            'bindings' => [
                                'processes_id'
                            ],
                            'target' => '_self',
                        ],
                        'label' => 'Name',
                        'width' => 4,
                        'searchable' => true,
                        'sortable' => true,
                        'color' => ''
                    ],
                    'process_type_metas_name' => [
                        'type' => 'text',
                        'type_options' => [],
                        'label' => 'Prozesstyp-Name',
                        'width' => 3,
                        'searchable' => true,
                        'sortable' => true,
                        'color' => ''
                    ],
                    'processes_created_at' => [
                        'type' => 'text',
                        'type_options' => [
                            'date_format' => 'd.m.Y H:i'
                        ],
                        'label' => 'Erstelldatum',
                        'width' => 2,
                        'searchable' => true,
                        'sortable' => true,
                        'color' => ''
                    ],
                    'process_types_version' => [
                        'type' => 'text',
                        'type_options' => [],
                        'label' => 'Version',
                        'width' => 2,
                        'searchable' => true,
                        'sortable' => false,
                        'color' => ''
                    ]
                ],
            ]
        ];
    }

    /**
     * @param array $attributes
     * @return ListConfig
     */
    public function make(array $attributes = []) {
        return new ListConfig(array_merge($this->state, $attributes));
    }

    /**
     * @param string $slug
     * @return ListConfigBuilder
     */
    public function withSlug(string $slug) {
        return $this->state([
            'slug' => $slug
        ]);
    }

    /**
     * @param string $template
     * @return ListConfigBuilder
     */
    public function withTemplate(string $template) {
        return $this->state([
            'template' => $template
        ]);
    }


    /**
     * Sets the source of the list config
     * @param string $sourceType
     * @param array $source
     * @return ListConfigBuilder
     */
    public function withSource(string $sourceType, array $source = []) {
        return $this->state([
            'data' => [
                ...$this->state['data'],
                ...
                [
                    'source_type' => $sourceType,
                    'source' => $source
                ]
            ]
        ]);
    }

    /**
     * @param array $data
     * @return ListConfigBuilder
     * @noinspection PhpUnused
     */
    public function withData(array $data) {
        return $this->state([
            'data' => [
                ...$this->state['data'],
                ...$data
            ]
        ]);
    }

}

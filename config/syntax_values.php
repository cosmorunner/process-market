<?php
/**
 * Available scope arguments for syntax values.
 */

return [
    'scope_arguments' => [
        'date' => [
            [
                'scope' => 'format',
                'label' => 'Format',
                'description' => '',
                'args' => [
                    [
                        'name' => null,
                        'type' => 'string'
                    ]
                ]
            ],
            [
                'scope' => 'add',
                'label' => 'Zeitinterval addieren',
                'description' => '',
                'args' => [
                    [
                        'label' => 'Minuten',
                        'name' => 'minutes',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Stunden',
                        'name' => 'hours',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Tage',
                        'name' => 'days',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Wochen',
                        'name' => 'weeks',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Monate',
                        'name' => 'months',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Jahre',
                        'name' => 'years',
                        'type' => 'integer'
                    ]
                ]
            ],
            [
                'scope' => 'sub',
                'label' => 'Zeitinterval addieren',
                'description' => '',
                'args' => [
                    [
                        'label' => 'Minuten',
                        'name' => 'minutes',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Stunden',
                        'name' => 'hours',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Tage',
                        'name' => 'days',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Wochen',
                        'name' => 'weeks',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Monate',
                        'name' => 'months',
                        'type' => 'integer'
                    ],
                    [
                        'label' => 'Jahre',
                        'name' => 'years',
                        'type' => 'integer'
                    ]
                ]
            ]
        ],
        'process.outputs' => [
            [
                'scope' => 2, // index of name of output field
                'label' => 'Wert formatieren',
                'description' => '',
                'args' => [
                    [
                        'label' => 'JSON Wert',
                        'description' => 'Wert aus JSON Objekt/Array mittels der Dot-Notation (z.B. "address.street").',
                        'name' => 'extract',
                        'type' => 'string'
                    ]
                ]
            ]
        ]
    ],
];

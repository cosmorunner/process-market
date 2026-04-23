<?php

namespace App\Utils;

use App\ProcessType\ActionType;
use App\ProcessType\Definition;

/**
 * Berechnet die Complexity Score für einen Graphen.
 * 0 = Niedrigste Komplexität, 10 = höchste Komplexität.
 */
class ComplexityScore {

    const VAR_ACTION_COUNT = 'ACTION_COUNT';
    const VAR_RULES_PER_ACTION_AVERAGE = 'RULES_PER_ACTION_AVERAGE';
    const VAR_IO_PER_ACTION_AVERAGE = 'IO_PER_ACTION_AVERAGE';
    const VAR_PROCESSORS_PER_ACTION_AVERAGE = 'PROCESSORS_PER_ACTION_AVERAGE';
    const VAR_STATUS_COUNT = 'STATUS_COUNT';
    const VAR_ROLES_COUNT = 'ROLES_COUNT';

    const VAR_TEMPLATES_COUNT = 'TEMPLATES_COUNT';
    const VAR_LIST_CONFIGS_COUNT = 'LIST_CONFIGS_COUNT';
    const VAR_EVENTS_COUNT = 'EVENTS_COUNT';
    const VAR_LISTENERS_COUNT = 'LISTENERS_COUNT';
    const VAR_DEPENDENCIES_COUNT = 'DEPENDENCIES_COUNT';

    /**
     * Gewichtigungen in Prozent zur Gesamt-Complexity Score.
     * Summe muss 100 ergeben.
     */
    const WEIGHTS = [
        self::VAR_ACTION_COUNT => 12.5,
        self::VAR_RULES_PER_ACTION_AVERAGE => 12.5,
        self::VAR_IO_PER_ACTION_AVERAGE => 12.5,
        self::VAR_PROCESSORS_PER_ACTION_AVERAGE => 12.5,
        self::VAR_STATUS_COUNT => 12.5,
        self::VAR_ROLES_COUNT => 12.5,
        self::VAR_TEMPLATES_COUNT => 5,
        self::VAR_LIST_CONFIGS_COUNT => 5,
        self::VAR_EVENTS_COUNT => 5,
        self::VAR_LISTENERS_COUNT => 5,
        self::VAR_DEPENDENCIES_COUNT => 5
    ];

    /**
     * Punkte pro Variable (Min=0, Max=10) gemessen an dem Wertebereich in dem der aktuelle Wert liegt.
     * Zu vergebene Punkte links, zu erfüllender Wertebereich rechts.
     */
    const POINTS_DISTRIBUTION = [
        self::VAR_ACTION_COUNT => [
            0 => [0, 0],
            3 => [1, 4],
            6 => [5, 9],
            10 => [10, 1000]
        ],
        self::VAR_RULES_PER_ACTION_AVERAGE => [
            0 => [0, 0],
            3 => [1, 2],
            6 => [3, 6],
            10 => [7, 1000]
        ],
        self::VAR_IO_PER_ACTION_AVERAGE => [
            0 => [0, 0],
            3 => [1, 4],
            6 => [5, 9],
            10 => [10, 1000]
        ],
        self::VAR_PROCESSORS_PER_ACTION_AVERAGE => [
            0 => [0, 0],
            3 => [1, 2],
            6 => [3, 6],
            10 => [7, 1000]
        ],
        self::VAR_STATUS_COUNT => [
            0 => [0, 0],
            3 => [1, 4],
            6 => [5, 9],
            10 => [10, 1000]
        ],
        self::VAR_ROLES_COUNT => [
            0 => [0, 0],
            3 => [1, 2],
            6 => [3, 6],
            10 => [7, 1000]
        ],
        self::VAR_TEMPLATES_COUNT => [
            0 => [0, 0],
            3 => [1, 2],
            6 => [3, 6],
            10 => [7, 1000]
        ],
        self::VAR_LIST_CONFIGS_COUNT => [
            0 => [0, 0],
            3 => [1, 4],
            6 => [5, 9],
            10 => [10, 1000]
        ],
        self::VAR_EVENTS_COUNT => [
            0 => [0, 0],
            3 => [1, 2],
            6 => [3, 6],
            10 => [7, 1000]
        ],
        self::VAR_LISTENERS_COUNT => [
            0 => [0, 0],
            3 => [1, 2],
            6 => [3, 6],
            10 => [7, 1000]
        ],
        self::VAR_DEPENDENCIES_COUNT => [
            0 => [0, 0],
            3 => [1, 4],
            6 => [5, 9],
            10 => [10, 1000]
        ],
    ];


    /**
     * Gibt die Punkte (0-10) für eine Variable zurück.
     * @param $variable
     * @param int|float $value
     * @return int
     */
    private static function points($variable, int|float $value): int {
        if (!in_array($variable, array_keys(self::POINTS_DISTRIBUTION))) {
            return 0;
        }

        if (is_float($value)) {
            $value = (int) round($value);
        }

        foreach (self::POINTS_DISTRIBUTION[$variable] as $points => $distribution) {
            if (in_array($value, range($distribution[0], $distribution[1]))) {
                return $points;
            }
        }

        return 0;
    }

    /**
     * Gibt die gewichteten Punkte für eine Variable zurück.
     * @param $variable
     * @param $points
     * @return int|float
     */
    private static function weightedPoints($variable, $points): int|float {
        if (!in_array($variable, array_keys(self::WEIGHTS))) {
            return 0;
        }

        $percentage = self::WEIGHTS[$variable];

        return ($percentage / 100) * $points;
    }

    /**
     * Berechnet die Komplexitäts-Score für eine Prozess-Definition.
     * @param Definition $definition
     * @return float
     */
    public static function calculcate(Definition $definition): float {
        $values = collect(self::WEIGHTS)->mapWithKeys(function ($item, $variable) use ($definition) {
            return [$variable => self::getValue($definition, $variable)];
        });

        $points = $values->map(fn($value, $variable) => self::points($variable, $value));
        $weightedPoints = $points->map(fn($value, $variable) => self::weightedPoints($variable, $value));

        // 0 - 10
        return round($weightedPoints->sum(), 1);
    }

    /**
     * Gibt den Wert zu einer Variable aus einer Definition zurück.
     * @param Definition $definition
     * @param string $variable
     * @return int|float
     */
    private static function getValue(Definition $definition, string $variable): int|float {
        if (!in_array($variable, array_keys(self::WEIGHTS))) {
            return 0;
        }

        switch ($variable) {
            case self::VAR_ACTION_COUNT:
                return $definition->actionTypes->count();
            case self::VAR_RULES_PER_ACTION_AVERAGE:
                $actionTypesCount = $definition->actionTypes->count();
                $totalRules = $definition->actionTypes->reduce(function (int $carry, ActionType $actionType) {
                    $actionRuleCount = $actionType->actionRules->count();
                    $statusRuleCount = $actionType->statusRules->count();

                    return $carry + $actionRuleCount + $statusRuleCount;
                }, 0);

                return $totalRules === 0 ? 0 : $totalRules / $actionTypesCount;
            case self::VAR_IO_PER_ACTION_AVERAGE:
                $actionTypesCount = $definition->actionTypes->count();
                $totalIO = $definition->actionTypes->reduce(function (int $carry, ActionType $actionType) {
                    $inputCount = $actionType->inputs->count();
                    $outputsCount = $actionType->outputs->count();

                    return $carry + $inputCount + $outputsCount;
                }, 0);

                return $totalIO === 0 ? 0 : $totalIO / $actionTypesCount;
            case self::VAR_PROCESSORS_PER_ACTION_AVERAGE:
                $actionTypesCount = $definition->actionTypes->count();
                $totalProcessors = $definition->actionTypes->reduce(function (int $carry, ActionType $actionType) {
                    return $carry + $actionType->processors->count();
                }, 0);

                return $totalProcessors === 0 ? 0 : $totalProcessors / $actionTypesCount;
            case self::VAR_STATUS_COUNT:
                return $definition->statusTypes->count();
            case self::VAR_ROLES_COUNT:
                return $definition->roles->count();
            case self::VAR_TEMPLATES_COUNT:
                return $definition->templates->count();
            case self::VAR_LIST_CONFIGS_COUNT:
                return $definition->listConfigs->count();
            case self::VAR_EVENTS_COUNT:
                return $definition->events->count();
            case self::VAR_LISTENERS_COUNT:
                return $definition->listeners->count();
            case self::VAR_DEPENDENCIES_COUNT:
                return collect($definition->dependencies)->flatten()->count();
            default:
                return 0;
        }
    }

}

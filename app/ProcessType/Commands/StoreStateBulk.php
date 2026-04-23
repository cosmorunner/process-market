<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Exception;

/**
 * Class StoreState
 * @package App\ProcessType\Commands
 */
class StoreStateBulk extends Command {

    /**
     * Recalculate graph
     * @var bool
     */
    public $recalculate = true;

    /**
     * Create multiple states
     * @return Definition
     * @throws Exception
     */
    public function command(): Definition {
        // First, we need to identify we highest max value provided in the rows. We need this value to not set the
        // min/max values of the unset rows to one min/max value of a set row.
        $highestProvidedMax = -PHP_INT_MAX;

        foreach ($this->payload['value'] as $row) {
            $parts = collect(explode(';', $row))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);

            // Fallback to min value is max is not set.
            $max = $parts[2] ?? $parts[1] ?? $highestProvidedMax;

            // Check if $max is greater than $highestProvidedMax.
            if (bccomp($max, $highestProvidedMax, StatusType::VALUE_PRECISION) === 1) {
                $highestProvidedMax = $max;
            }
        }

        // We round the highest max value and add 1.
        $highestProvidedMax = ((int) number_format($highestProvidedMax)) + 1;

        foreach ($this->payload['value'] as $row) {
            $statusType = $this->definition->statusType($this->payload['status_type_id']);
            $parts = collect(explode(';', $row))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);
            $description = $parts[0];

            // If "min" value is set.
            // Set max to min, if max is not set.
            if ($parts[1] ?? '') {
                $min = State::validateValue($parts[1]);
                $max = State::validateValue($parts[2] ?? $parts[1]);

            }
            else {
                // Here, we get the next min/max value based on the existing states and relative to the provided min/max in the rows.
                $minMax = State::nextMinMax($statusType, $highestProvidedMax);
                $min = $minMax['min'];
                $max = $minMax['max'];
            }

            $payload = [
                'color' => '#72c6ff',
                'description' => $description,
                'image' => 'arrow_forward',
                'min' => $min,
                'max' => $max,
                'status_type_id' => $this->payload['status_type_id']
            ];

            $this->definition = (new StoreState($payload, $this->definition, $this->processVersion))->execute();
        }

        return $this->definition;
    }
}

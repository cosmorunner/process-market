<?php

namespace App\Rules;

use App\Models\ProcessVersion;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class ValidStoreStateBulkSyntax
 * @package App\Rules
 */
class ValidStoreStateBulkSyntax implements ValidationRule {

    /**
     * Statustype where new states are created.
     * @var StatusType|null
     */
    private ?StatusType $statusType;

    /**
     * All rows of the bulk create.
     * @var array
     */
    private array $allRows;

    /**
     * Row index in the bulk validation.
     * @var int
     */
    private int $rowIndex = 0;

    /**
     * @param string $statusTypeId
     * @param array $allRows All rows of the bulk create
     */
    public function __construct(string $statusTypeId, array $allRows) {
        /* @var ProcessVersion $processVersion */
        $processVersion = request('processVersion');
        $this->allRows = $allRows;
        $this->statusType = $processVersion->definition->statusType($statusTypeId);
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     * @throws Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $this->rowIndex = (int) (explode('.', $attribute)[1]);
        $rowParts = collect(explode(';', $value))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);

        // When checking overlapping states, we need to check against the existing states, and the other rows of the bulk create.
        // We instantiate State objekts, to give the collection to NoOverlappingStates.
        $otherRowStatesWithMinMax = collect($this->allRows)
            // We filter current item
            ->filter(fn($row, $key) => $key != $this->rowIndex)
            // We map to row parts array
            ->map(fn($row) => collect(explode(';', $value))
                ->map(fn($item) => trim($item, ' '))
                ->filter(fn($item) => $item)
                ->toArray())
            // We remove items that to not have a min value (value will be set automatically so no need to validate).
            ->filter(fn($rowArray) => array_key_exists(1, $rowArray) && empty($rowArray[1]))
            // We map each row array to a State object.
            ->map(fn($rowArray) => State::make([
                'description' => $rowArray[0],
                'min' => $rowArray[1],
                'max' => $rowArray[2] ?? $rowArray[1]
            ]));

        // We add existing State objects and State objects "to be created".
        $statesToCheckOverlapping = $this->statusType->states->concat($otherRowStatesWithMinMax);

        // If no max value is set, we set it to min value, or to null if neither is set.
        $row = ['description' => $rowParts[0]];

        if (isset($rowParts[1]) && !empty($rowParts[1])) {
            $row['min'] = $rowParts[1];
        }

        if (isset($rowParts[2]) && !empty($rowParts[2])) {
            $row['max'] = $rowParts[2];
        }

        $validator = Validator::make($row, [
            'description' => ['bail', 'required', 'string', 'max:200'],
            'min' => [
                'bail',
                'filled',
                Rule::requiredIf(fn() => isset($row['max'])),
                new StatusValueFormat,
                new MinEqualOrLowerThanMax($row['max'] ?? $row['min'] ?? null),
                new NoOverlappingStates($statesToCheckOverlapping)
            ],
            'max' => [
                'bail',
                'filled',
                new StatusValueFormat,
                new MaxEqualOrGreaterThanMin($row['min'] ?? null),
                new NoOverlappingStates($statesToCheckOverlapping)
            ],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}

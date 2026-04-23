<?php

namespace App\Rules;

use App\ProcessType\Definition;
use App\ProcessType\Processor;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prüft ob die Maximale Anzahl an Prozessoren von einem Typ erreicht wurde. Beispielsweise darf es den "redirect"
 * Prozessor nur einmal pro Aktion geben.
 * Class ProcessorMaxCount
 * @package App\Rules
 */
class ProcessorMaxCount implements ValidationRule {

    /**
     * ProcessorMaxCount constructor.
     * @param Definition $definition
     * @param string $actionTypeId
     * @param string|null $processorId
     */
    public function __construct(private readonly Definition $definition, private readonly string $actionTypeId, private readonly string|null $processorId = null) {
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $actionType = $this->definition->actionType($this->actionTypeId);
        $currentCount = $actionType ? $actionType->processors->where('identifier', '=', $value)->count() : PHP_INT_MAX;
        $allowedMaxCount = Processor::MAX_ITEMS[$value] ?? 1;

        // Falls ein bestehender Prozessor verändert wird, soll dieser von der Anzahl abgezogen werden.
        $updatedProcessor = $actionType->processor((string) $this->processorId);

        if ($updatedProcessor && $updatedProcessor->identifier === $value && $updatedProcessor->id === $this->processorId) {
            $currentCount--;
        }

        if ($allowedMaxCount <= $currentCount) {
            $fail('Sie können keine weiteren Prozessoren von diesem Typ zur Aktion hinzufügen.');
        }
    }

}

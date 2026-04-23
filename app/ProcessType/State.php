<?php

namespace App\ProcessType;

use App\Exceptions\InvalidConfigurationException;
use Exception;
use Ramsey\Uuid\Uuid;
use Str;

/**
 * Class State
 * @package App\ProcessType
 */
class State extends AbstractModel {

    public string $id;
    public string $status_type_id;
    public ?StatusType $statusType;
    public string $description;
    public ?string $image;
    public string $min;
    public string $max;
    public bool $hidden = false;
    public ?string $color;
    public bool $hasCustomValue = false;
    public bool $hasValueRange = false;
    public array $action_type_links = [];

    /**
     * Prüft und säubert den Wert.
     * @param string $value
     * @return string
     * @throws Exception
     */
    public static function validateValue($value) {
        // Komma mit Punkt ersetzen, Wert trimmen und anfängliche Nullen entfernen.
        $value = str_replace(',', '.', (string) $value);

        if ($value !== '0') {
            $value = ltrim(trim($value), '0');
        }

        if (Str::startsWith($value, '.')) {
            $value = '0' . $value;
        }

        // Sollte die Zahl zu viele Nachkommastellen haben, wird diese formatiert.
        $format = sprintf('/^[-]?[0-9]{1,%s}([.][0-9]{%s,})?$/', StatusType::VALUE_MAX_LENGTH, StatusType::VALUE_PRECISION + 1);
        if (preg_match($format, $value)) {
            $value = as_decimal(str_replace(',', '.', $value), StatusType::VALUE_PRECISION);
        }

        // Nur Zahlen, Komma oder Punkt. z.B. 4.292 oder 3945821.3 oder 120
        $format = sprintf('/^[-]?[0-9]{1,%s}([.][0-9]{0,%s})?$/', StatusType::VALUE_MAX_LENGTH, StatusType::VALUE_PRECISION);
        if (!preg_match($format, $value)) {
            throw new InvalidConfigurationException(__('exceptions.statusrule_invalid_value', ['value' => $value]));
        }

        // Nur wenn die Nachkommastellen nicht korrekt sind, Zahl formatieren
        $format = sprintf('/^[-]?[0-9]{1,%s}([.][0-9]{%s})?$/', StatusType::VALUE_MAX_LENGTH, StatusType::VALUE_PRECISION);
        if (!preg_match($format, $value)) {
            $value = as_decimal($value, StatusType::VALUE_PRECISION);
        }

        return $value;
    }

    /**
     * Prüft ob ein Wert dem notwendingen Format entspricht.
     * @param string $value
     * @return bool
     */
    public static function isValidValueFormat(string $value) {
        try {
            self::validateValue($value);

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    /**
     * Leeres Status-Typ Datenmodel.
     * @param array $options
     * @return State
     * @noinspection PhpUnhandledExceptionInspection
     * @throws Exception
     */
    public static function make(array $options = []) {
        $id = array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()
            ->toString();

        return new self([
            'id' => $id,
            'status_type_id' => $options['status_type_id'] ?? Uuid::uuid4()->toString(),
            'description' => $options['description'] ?? '',
            'image' => $options['image'] ?? '',
            'hidden' => (bool) ($options['hidden'] ?? false),
            'color' => $options['color'] ?? '',
            'min' => State::validateValue($options['min'] ?? '0.000'),
            'max' => State::validateValue($options['max'] ?? '0.000'),
            'action_type_links' => $options['action_type_links'] ?? []
        ]);
    }

    /**
     * Based on the existing states and the default value for a statustype min/max values can be calculated.
     * Used for the StoreState and StoreStateBulk command where min/max values are optional.
     * Rules:
     * - If states is empty, min/max is equal to the default value.
     * - It state are set, min/max is set to the highest existing "max" value + 1
     * @param StatusType $statusType
     * @param int|null $minimum Optional minimum integer. Set in create bulk command, when next min/max is relative to multiple rows,
     * that might have min/max already set
     * @return array{min: String, max: String}
     * @throws Exception
     */
    public static function nextMinMax(StatusType $statusType, int $minimum = null): array {
        if ($statusType->states->isEmpty()) {
            return [
                'min' => $statusType->default,
                'max' => $statusType->default
            ];
        }


        $highestMaxValue = (int) number_format($statusType->states->pluck('max')->sort()->last());

        // Check if $minimum is greater than $highestMaxValue.
        if (is_int($minimum) && bccomp($minimum, $highestMaxValue, StatusType::VALUE_PRECISION) === 1) {
            $highestMaxValue = $minimum;
        } else {
            $highestMaxValue++;
        }

        return [
            'min' => State::validateValue((string) $highestMaxValue),
            'max' => State::validateValue((string) $highestMaxValue)
        ];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'status_type_id' => $this->status_type_id,
            'description' => $this->description,
            'image' => $this->image,
            'min' => $this->min,
            'max' => $this->max,
            'hidden' => $this->hidden,
            'color' => $this->color ?? '',
            'action_type_links' => $this->action_type_links
        ];
    }

}

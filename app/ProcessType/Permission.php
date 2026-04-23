<?php

namespace App\ProcessType;

use App\Interfaces\Iconable;
use Ramsey\Uuid\Uuid;

/**
 * Class Permission
 * @package App\ProcessType
 */
class Permission extends AbstractModel implements Iconable {

    public string $id;
    public string $name;
    public string $description;
    public string $ident;
    public array $conditions = [];

    /**
     * @inheritDoc
     */
    public static function icon(): string {
        return 'done';
    }

    /**
     * Erzeugt ein neues Role-Object mit Standardwerten.
     * @param array $options
     * @return Permission
     */
    public static function make(array $options) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'name' => $options['name'] ?? $options['ident'] ?? 'demo',
            'description' => trim($options['description'] ?? ''),
            'ident' => $options['ident'] ?? '',
            'conditions' => $options['conditions'] ?? [],
        ]);
    }

    /**
     * Ersetzt bei einer Permission-Ident die UUIDs mit "*".
     * @param $ident
     * @return string
     */
    public static function identToTemplate($ident) {
        return collect(explode('.', $ident))->map(function ($part) {
            if (Uuid::isValid($part)) {
                return '*';
            }

            return $part;
        })->join('.');
    }

    /**
     * Replaces "*" with $ident in a Permission-Template.
     * @param $templete
     * @param $ident
     * @return string
     */
    public static function templateToIdent($templete, $ident) {
        return collect(explode('.', $templete))->map(function ($part) use ($ident) {
            if ($part === '*') {
                return $ident;
            }

            return $part;
        })->join('.');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'ident' => $this->ident,
            'conditions' => $this->conditions,
        ];
    }
}


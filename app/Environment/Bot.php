<?php /** @noinspection PhpUnused */

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation eines System-Bots. Wird für die Environmenterzeugung genutzt.
 * Class Bot
 * @package App\Environment
 */
class Bot extends AbstractModel {

    public string $id;
    public string $first_name;
    public array $aliases = [];

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Bot
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'first_name' => $options['first_name'] ?? '',
            'aliases' => $options['aliases'] ?? [],
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'aliases' => $this->aliases,
        ];
    }
}

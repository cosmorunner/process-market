<?php /** @noinspection PhpUnused */

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation einer PublicApi-Konfiguration. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class PublicApi extends AbstractModel {

    public string $id;
    public string $name;
    public string $slug;
    public ?string $type;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return PublicApi
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'name' => $options['name'] ?? '',
            'slug' => $options['slug'] ?? '',
            'type' => $options['type'] ?? '',
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
        ];
    }
}

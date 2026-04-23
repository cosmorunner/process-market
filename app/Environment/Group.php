<?php /** @noinspection PhpUnused */

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation einer System-Gruppe. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class Group extends AbstractModel {

    public string $id;
    public string $name;
    public string|null $provider = null;
    public string|null $provider_group_id = null;
    public array $aliases = [];
    public array $tags = [];
    public string|null $identity_process_type;
    public string|null $identity_process_instance;


    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Group
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'name' => $options['name'] ?? '',
            'provider' => $options['provider'] ?? null,
            'provider_group_id' => $options['provider_group_id'] ?? null,
            'aliases' => $options['aliases'] ?? [],
            'tags' => $options['tags'] ?? [],
            'identity_process_type' => $options['identity_process_type'] ?? '',
            'identity_process_instance' => $options['identity_process_instance'] ?? '',
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'provider' => $this->provider,
            'provider_group_id' => $this->provider_group_id,
            'aliases' => $this->aliases,
            'tags' => $this->tags,
            'identity_process_type' => $this->identity_process_type ?? '',
            'identity_process_instance' => $this->identity_process_instance ?? '',
        ];
    }
}
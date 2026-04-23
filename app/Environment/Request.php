<?php

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation eines Connector-Requests. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class Request extends AbstractModel {

    public ?string $description;
    public ?string $method;
    public ?string $uri;
    public array $bindings;
    public array $body;
    public array $debug_options;
    public array $headers;
    public array $validation;
    public bool $active;
    public string $connector_id;
    public string $id;
    public string $identifier;
    public string $name;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Request
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'connector_id' => $options['connector_id'] ?? Uuid::uuid4()->toString(),
            'name' => $options['name'] ?? 'my_connector',
            'description' => $options['description'] ?? '',
            'identifier' => $options['identifier'] ?? 'my_identifier',
            'uri' => $options['uri'] ?? '/',
            'method' => $options['method'] ?? 'GET',
            'active' => $options['active'] ?? true,
            'bindings' => $options['bindings'] ?? [],
            'headers' => $options['headers'] ?? [],
            'body' => $options['body'] ?? [],
            'validation' => $options['validation'] ?? [],
            'debug_options' => $options['debug_options'] ?? [],
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'active' => $this->active,
            'bindings' => $this->bindings,
            'body' => $this->body,
            'connector_id' => $this->connector_id,
            'debug_options' => $this->debug_options,
            'description' => $this->description,
            'headers' => $this->headers,
            'id' => $this->id,
            'identifier' => $this->identifier,
            'method' => $this->method,
            'name' => $this->name,
            'uri' => $this->uri,
            'validation' => $this->validation,
        ];
    }
}

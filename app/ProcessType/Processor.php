<?php

namespace App\ProcessType;

use Ramsey\Uuid\Uuid;

/**
 * Class Processor
 * @package App\ProcessType
 */
class Processor extends AbstractModel {

    public string $id;
    public string $action_type_id;
    public string $identifier;
    public array $conditions = [];
    public array $options = [];
    public bool $required = true;
    public $type;
    public ?int $sort = null;

    /**
     * Maximale Anzahl von Prozessoren pro Typ und Aktionstyp.
     */
    const MAX_ITEMS = [
        'create_access' => 15,
        'create_process' => 10,
        'create_relation' => 10,
        'create_document' => 10,
        'copy_artifact' => 10,
        'delete_access' => 10,
        'delete_relation' => 10,
        'execute_action' => 10,
        'send_email' => 10,
        'send_push_message' => 15,
        'update_process_meta' => 5,
        'trigger_connector' => 20,
        'redirect' => 1,
        'execute_custom_logic' => 15,
        'trigger_event' => 10,
        'trigger_task' => 10,
        'display_flash_message' => 10,
        'tag_action' => 1,
        'delete_process' => 5,
        'create_e_invoice' => 10
    ];

    /**
     * Namen der Prozessoren.
     * @return string[]
     */
    public static function names() {
            return [
                'create_access' => 'Zugriff erteilen',
                'create_process' => 'Prozess-Instanz erstellen',
                'delete_process' => 'Prozess-Instanzen löschen',
                'create_relation' => 'Verknüpfung erstellen / aktualisieren',
                'create_document' => 'Dokument erzeugen',
                'copy_artifact' => 'Artefakt kopieren',
                'delete_access' => 'Zugriff entziehen',
                'delete_relation' => 'Verknüpfung entfernen',
                'execute_action' => 'Aktion ausführen',
                'send_email' => 'E-Mail versenden',
                'display_flash_message' => 'Flash-Nachricht anzeigen',
                'send_push_message' => 'Benachrichtigung versenden',
                'update_process_meta' => 'Prozess-Metadaten ändern',
                'trigger_connector' => 'Connector-Anfrage ausführen',
                'redirect' => 'Weiterleitung',
                'execute_custom_logic' => 'Eigene Logik ausführen',
                'trigger_event' => 'Event auslösen',
                'trigger_task' => 'Aufgabe ausführen',
                'tag_action' => 'Aktion markieren',
                'create_e_invoice' => 'E-Rechnung erstellen'
            ];
    }

    /**
     * Erzeugt ein neues Processor-Objekt mit Standardwerten
     * @param array $options
     * @return Processor
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'action_type_id' => $options['action_type_id'] ?? '',
            'identifier' => $options['identifier'] ?? '',
            'options' => $options['options'] ?? [],
            'required' => $options['required'] ?? true,
            'sort' => $options['sort'] ?? null,
            'conditions' => $options['conditions'] ?? []
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'action_type_id' => $this->action_type_id,
            'identifier' => $this->identifier,
            'conditions' => $this->conditions,
            'options' => $this->options,
            'required' => $this->required,
            'sort' => $this->sort,
        ];
    }
}


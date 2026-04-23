<?php /** @noinspection PhpUnused */

namespace App\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\Models\Setting;
use App\ProcessType\Definition;
use Ramsey\Uuid\Uuid;

/**
 * Class PasteElement
 * @package App\ProcessType\Commands
 */
class PasteElement extends Command {

    /**
     * The stored copy element.
     * @var array
     */
    private array $copyElement;

    /**
     * Führt einen Command aus. Wird für die Bearbeitung der Graph-Definition genutzt.
     * @return Definition
     */
    public function command(): Definition {
        $this->copyElement = Setting::retrieveUser('copy_element', []);

        return $this->definition;
    }

    /**
     * Gibt zusätzliche Commands zurück die ebenfalls nach der Command-Ausführung ausgeführt werden sollen.
     * @param Definition $updatedDefinition Die bereits durch den ursprünglichen Command aktualisierte Definition
     * @return Command[]
     */
    protected function afterExecutingCommands(Definition $updatedDefinition): array {
        $name = $this->payload['name'];

        if ($name !== ($this->copyElement['name'] ?? '')) {
            return [];
        }

        $command = match ($name) {
            'allisa/form', 'allisa/collection', 'allisa/progress-bar', 'allisa/file-preview' => $this->pasteAllisaComponent($updatedDefinition),
            'list_config' => $this->pasteListConfig($updatedDefinition),
            'event' => $this->pasteEvent($updatedDefinition),
            'relation_type' => $this->pasteRelationType($updatedDefinition),
            'template' => $this->pasteTemplate($updatedDefinition),
            default => [],
        };

        Setting::deleteUserSetting('copy_element');

        return $command;
    }

    /**
     * @param Definition $updatedDefinition
     * @return array|StoreComponent[]
     */
    private function pasteAllisaComponent(Definition $updatedDefinition): array {
        $actionTypeId = $this->payload['options']['action_type_id'] ?? '';

        if ($actionTypeId === '') {
            return [];
        }

        $component = $this->copyElement['object'];
        $component['id'] = Uuid::uuid4()->toString();
        $component['label'] = trim($component['label'] ?? '') ? trim($component['label'] . ' Kopie') : '';
        $component['action_type_id'] = $actionTypeId;

        return [(new StoreComponent($component, $updatedDefinition, $this->processVersion))];
    }

    /**
     * @param Definition $updatedDefinition
     * @return StoreListConfig[]
     */
    private function pasteListConfig(Definition $updatedDefinition): array {
        $list = $this->copyElement['object'];
        $list['id'] = Uuid::uuid4()->toString();
        $list['name'] = $list['name'] . ' kopie';
        $list['slug'] = $list['slug'] . '-kopie';

        return [(new StoreListConfig($list, $updatedDefinition, $this->processVersion))];
    }

    /**
     * @param Definition $updatedDefinition
     * @return StoreEvent[]
     */
    private function pasteEvent(Definition $updatedDefinition): array {
        $event = $this->copyElement['object'];
        $event['id'] = Uuid::uuid4()->toString();
        $event['name'] = $event['name'] . '_kopie';

        return [(new StoreEvent($event, $updatedDefinition, $this->processVersion))];
    }

    /**
     * @param Definition $updatedDefinition
     * @return StoreRelationType[]
     */
    private function pasteRelationType(Definition $updatedDefinition): array {
        $relationType = $this->copyElement['object'];
        $relationType['id'] = Uuid::uuid4()->toString();
        $relationType['name'] = $relationType['name'] . ' Kopie';

        return [(new StoreRelationType($relationType, $updatedDefinition, $this->processVersion))];
    }

    /**
     * @param Definition $updatedDefinition
     * @return CopyTemplate[]
     */
    private function pasteTemplate(Definition $updatedDefinition): array {
        $template = $this->copyElement['object'];

        return [(new CopyTemplate($template, $updatedDefinition, $this->processVersion))];
    }

    /**
     * Update the preview datasets of the templates.
     * @param ProcessVersion $processVersion
     * @param array $payload
     * @return void
     */
    public function afterCommandExecution(ProcessVersion $processVersion, array $payload): void {
        $processVersion->syncTemplatePreviewDatasets();
    }
}

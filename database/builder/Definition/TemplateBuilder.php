<?php

namespace Database\Builder\Definition;

use App\ProcessType\Template;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class TemplateBuilder
 * @package Database\Builder\Definition
 */
class TemplateBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'Demo template',
            'data' => '',
            'type' => Template::TYPE_HTML,
            'mapping' => [],
            'stylesheets' => ['bootstrap-4-6']
        ];
    }

    /**
     * @param array $attributes
     * @return Template
     */
    public function make(array $attributes = []) {
        $template = new Template(array_merge($this->state, $attributes));
        $template->data = base64_encode($template->data);

        return $template;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function withName(string $name) {
        return $this->state([
            'name' => $name
        ]);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function withData(string $data) {
        return $this->state([
            'data' => $data
        ]);
    }

    /**
     * @return $this
     */
    public function withTypeCustomLogic() {
        return $this->state([
            'type' => Template::TYPE_CUSTOM_LOGIC
        ]);
    }

    /**
     * @param array $mapping
     * @return $this
     */
    public function withMapping(array $mapping) {
        return $this->state([
            'mapping' => $mapping
        ]);
    }
}

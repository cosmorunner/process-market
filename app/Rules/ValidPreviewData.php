<?php

namespace App\Rules;

use App\ProcessType\Template;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

/**
 * Class ValidPreviewData
 * @package App\Rules
 */
class ValidPreviewData implements ValidationRule {

    /**
     * The template that is being rendered.
     * @var Template
     */
    private Template $template;

    /**
     * @param Template $template
     */
    public function __construct(Template $template) {
        $this->template = $template;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $attribute = str_starts_with($attribute, 'values.') ? substr($attribute, 7) : $attribute;
        $mappingOptions = $this->template->mapping[$attribute] ?? [];
        $type = $mappingOptions['type'] ?? null;

        // If the value does not exist in template mapping it is ok. Once the template is updated, the preview datasets are synced.
        if (!$type) {
            return;
        }

        if ($type === Template::MAPPING_TYPE_STRING) {
            if (!(!is_array($value) && !is_object($value))) {
                $fail($this->message());
            }

            return;
        }

        if ($type === Template::MAPPING_TYPE_ARRAY || $type === Template::MAPPING_TYPE_LIST_CONFING) {
            if (!is_array($value)) {
                $fail($this->message());
            }

            return;
        }

        if ($type === Template::MAPPING_TYPE_GROUP || $type === Template::MAPPING_TYPE_USER) {
            if (!is_string($value) && !is_null($value)) {
                $fail($this->message());
            }

            return;
        }

        // For the "mustache_list_columns" template, there is only one mapping, called "js", which is JavaScript that must
        // return an object. So for the preview data, the value for "js" must be an associative array.
        if ($type === Template::MAPPING_TYPE_JS) {
            if (!is_array($value) || Arr::isList($value)) {
                $fail($this->message());
            }

            return;
        }

        $fail($this->message());
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültiger Wert.';
    }
}

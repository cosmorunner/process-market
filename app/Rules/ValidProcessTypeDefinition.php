<?php

namespace App\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidProcessTypeDefinition
 * @package App\Rules
 */
class ValidProcessTypeDefinition implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {

        // Entweder als Datei oder als String
        if (!$value instanceof UploadedFile && !is_string($value)) {
            $fail($this->message());
        }

        try {
            $content = $value instanceof UploadedFile ? File::get($value->getRealPath()) : (string) $value;
            Validator::make(['content' => $content], ['content' => 'required|string|json'])->validate();
        }
        catch (Exception) {
            $fail($this->message());

            return;
        }

        $definition = json_decode($content, true);

        $validator = Validator::make($definition, [
            'name' => ['required', 'string'],
            'description' => ['string'],
            'namespace' => ['required', 'string'],
            'identifier' => ['required', 'string'],
            'image' => ['nullable', 'string'],
            'version' => ['required', 'string'],
            'reference_pattern' => ['string'],
            'action_type_mapping' => ['array'],
            'default_role_id' => ['nullable', 'uuid'],
            'public_role_id' => ['nullable', 'uuid'],
            'published_at' => ['nullable', 'date'],
            'outputs' => ['array'],
            'status_types' => ['array'],
            'action_types' => ['array'],
            'roles' => ['array'],
            'list_configs' => ['array'],
            'menu_items' => ['array'],
            'templates' => ['array'],
            'relation_types' => ['array'],
            'categories' => ['array'],
            'events' => ['array'],
            'listeners' => ['array'],
            'dependencies' => ['array']
        ]);

        if (!$validator->passes()) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültige Prozesstyp-Definition.';
    }
}

<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\ListConfig;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateListConfig
 * @package App\Rules\ProcessType
 */
class UpdateListConfig implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    /**
     * @var Definition
     */
    private Definition $definition;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $listConfigs = $this->definition->listConfigs;
        $invalidSlugs = $listConfigs->filter(fn(ListConfig $listConfig) => ($value['id'] ?? null) !== $listConfig->id)
            ->pluck('slug');

        $validator = Validator::make((array) $value, [
            'id' => ['bail', 'required', 'uuid', Rule::in($listConfigs->pluck('id'))],
            'name' => ['bail', 'required', 'string', 'max:64'],
            'description' => ['nullable', 'string', 'max:300'],
            'slug' => ['bail', 'required', 'string', Rule::notIn($invalidSlugs)],
            'template' => ['bail', 'nullable', 'string', Rule::in(array_keys(config('list_templates')))],
            'data' => ['array'],
            'data.limit_to_user_involvement' => ['nullable', 'boolean'],
            'data.rows_per_page' => ['required', 'integer', 'min:1'],
            'data.header_button' => ['array'],
            'data.enable_label' => ['required', 'boolean'],
            'data.enable_search' => ['required', 'boolean'],
            'data.enable_pagination' => ['required', 'boolean'],
            'data.enable_total_count' => ['required', 'boolean'],
            'data.quick_filter' => ['array'],
            'data.source_type' => ['required', 'in:sql,sql_users,sql_groups,sql_actions,relation,connector_request,accesses'],
            'data.source' => ['required', 'array'],
            'data.columns' => ['array']
        ], [
            'slug.not_in' => 'Dieser Slug existiert bereits.'
        ], [
            'slug' => 'Slug',
            'template' => 'Vorlage',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}

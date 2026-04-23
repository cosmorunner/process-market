<?php

namespace App\Http\Requests;

use App\Models\ProcessVersion;
use App\ProcessType\Template;
use App\Rules\ValidPreviewData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PreviewTemplate
 * @package App\Http\Requests
 */
class PreviewTemplate extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var ProcessVersion $processVersion */
        $processVersion = request()->route('processVersion');
        $templateId = request()->route('templateId');
        $template = $processVersion->definition->template($templateId);

        if (!$template instanceof Template) {
            return [];
        }

        return [
            'template' => ['required', 'string'],
            'data' => ['array'],
            'data.*' => [new ValidPreviewData($template)],
            'options' => ['nullable','array'],
            'options.output' => ['nullable', 'string', 'in:html,pdf,json']
        ];
    }
}

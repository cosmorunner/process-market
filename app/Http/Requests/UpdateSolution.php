<?php

namespace App\Http\Requests;

use App\Enums\Visibility;
use App\Models\Solution;
use App\Traits\UsesFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSolution
 * @package App\Http\Requests
 */
class UpdateSolution extends FormRequest {

    use UsesFailedValidationJsonResponse;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var Solution $solution */
        $solution = request('solution');

        // Nur ein Prozess, der bereits eine fertiggestellte Version hat, kann öffentlich gemacht werden.
        if ($solution->hasPublishedVersion()) {
            // Erst wenn Lizenzmanagement fertig ist
            $options = [Visibility::Private->value, Visibility::Hidden->value, Visibility::Public->value];
        }
        else {
            $options = [Visibility::Private->value];
        }

        return [
            'name' => ['required', 'min:3'],
            'description' => ['nullable', 'string', 'max:1000'],
            'visibility' => ['bail', 'required', 'in:' . implode(',', $options)],
            'tags' => ['nullable', 'array', 'max:3'],
            'tags.*' => ['string', 'max:20'],
        ];
    }

    /**
     * @return array
     */
    public function attributes() {
        return [
            'visibility' => 'Sichtbarkeit',
        ];
    }

}

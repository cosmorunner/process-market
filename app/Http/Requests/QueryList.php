<?php

namespace App\Http\Requests;

use App\ListQuery\QueryParams;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class QueryList
 * @package App\Http\Requests
 */
class QueryList extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            QueryParams::QUERY_SEARCH => ['nullable', 'string'],
            QueryParams::QUERY_SORT => ['nullable', 'string'],
            QueryParams::QUERY_ARCHIVED => ['nullable', 'string'],
        ];
    }

}
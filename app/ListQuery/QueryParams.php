<?php

namespace App\ListQuery;

use App\Http\Requests\QueryList;

/**
 * Wrapper class for list query parameters
 */
class QueryParams {
    const QUERY_SEARCH = 'search';
    const QUERY_SORT = 'sort';
    const QUERY_ARCHIVED = 'archived';


    const QUERY_PARAMS = [
        self::QUERY_SEARCH,
        self::QUERY_SORT,
        self::QUERY_ARCHIVED
    ];

    public array $values;

    /**
     * Instantiates a new QueryParams object.
     * @param array|QueryList|QueryParams $params
     */
    public function __construct(array|QueryList|QueryParams $params) {
        if ($params instanceof QueryList) {
            $query = is_array($params->query()) ? $params->query() : [];

            $this->values = array_merge($query, $params->validated());
        }
        else if ($params instanceof QueryParams) {
            $this->values = $params->values();
        }
        else {
            $this->values = $params;
        }
    }

    /**
     * All query params.
     * @return array
     */
    public function values(): array {
        return $this->values;
    }

    /**
     * Flag whether a search query exists in the request.
     */
    public function hasSearchQuery() {
        return array_key_exists(self::QUERY_SEARCH, $this->values);
    }

    /**
     * Flag whether a sorting exists in the request.
     * @return bool
     */
    public function hasSortQuery() {
        return array_key_exists(self::QUERY_SORT, $this->values);
    }

    /**
     * Flag whether archived exists in the request.
     * @return bool
     */
    public function hasArchivedQuery() {
        return array_key_exists(self::QUERY_SORT, $this->values);
    }

    /**
     * "search"-Paramenter
     * @return string|null
     */
    public function searchQuery() {
        return $this->values[self::QUERY_SEARCH] ?? '';
    }

    /**
     * "sort"-Parameter
     * @return string|null
     */
    public function sortQuery() {
        return $this->values[self::QUERY_SORT] ?? '';
    }

    /**
     * "archived"-Parameter
     * @return string|null
     */
    public function archivedQuery() {
        return $this->values[self::QUERY_ARCHIVED] ?? '0';
    }
}
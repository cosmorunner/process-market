<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

/**
 * Class TagsController
 * @package App\Http\Controllers\Api
 */
class TagsController extends Controller {


    /**
     * Gibt alle Tags anhand einer Suche zurück.
     * @return JsonResponse
     */
    public function index() {
        $query = request('query') ?? '';

        if (!is_string($query) || strlen($query) < 2) {
            return response()->json();
        }

        $tags = Tag::query()->select(['name', 'color'])
            ->where('name', 'like', '%' . $query . '%')
            ->limit(5)->get();

        return response()->json($tags);
    }

}

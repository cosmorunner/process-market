<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Template as TemplateResource;
use App\Models\Template;
use Illuminate\Http\JsonResponse;

/**
 * Class TemplatesController
 * @package App\Http\Controllers\Api
 */
class TemplatesController extends Controller {

    /**
     * Alle Vorlagen
     * @return JsonResponse
     */
    public function index() {
        return response()->json(TemplateResource::collection(Template::all()));
    }

}

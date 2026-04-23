<?php /** @noinspection PhpUnused */

namespace App\Http\Controllers\Api;

use App\DemoConnector;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSolutionVersion;
use App\Models\SolutionVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

/**
 * Class SolutionVersionController
 * @package App\Http\Controllers\Api
 */
class SolutionVersionsController extends Controller {

    /**
     * "data"-Attribut einer Lösungsversion aktualisieren.
     * @param UpdateSolutionVersion $request
     * @param SolutionVersion $solutionVersion
     * @return JsonResponse
     */
    public function update(UpdateSolutionVersion $request, SolutionVersion $solutionVersion) {
        $data = $request->validated()['data'] ?? [];
        $currentData = $solutionVersion->data;
        $solutionVersion->update(['data' => $data]);

        if (!$mainDemo = $solutionVersion->mainDemo()) {
            return response()->json(['message' => 'Admin-Demo existiert nicht.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $connector = new DemoConnector($mainDemo);
            $connector->syncProcessTypes();
        }
        catch (Throwable $throwable) {
            report($throwable);

            // Vorherige Daten wieder setzen
            $solutionVersion->update(['data' => $currentData]);

            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($solutionVersion->attributesToArray());
    }


}

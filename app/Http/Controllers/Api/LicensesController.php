<?php /** @noinspection PhpUnused */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProcessLicense;
use App\Http\Requests\StoreSolutionLicense;
use App\Http\Resources\SolutionLicense;
use App\Interfaces\Licensable;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Url\Url;
use Throwable;

/**
 * Class LicensesController
 * @package App\Http\Controllers\Api
 */
class LicensesController extends Controller {

    /**
     * Erstellt und speichert eine Lizenz für einen Prozess.
     * @param StoreProcessLicense $request
     * @return JsonResponse
     */
    public function storeProcessLicense(StoreProcessLicense $request) {
        /* @var Licensable|Model|Process $model */
        /* @var Licensable $resource */
        /* @var User|Organisation $receiver */

        $validated = $request->validated();
        $resourceId = $validated['resource_id'];
        $resourceType = $validated['resource_type'];

        if (!class_exists($resourceType) || !new $resourceType() instanceof Licensable) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        $model = $resourceType::make();
        $resource = $model::find($resourceId);
        $receiver = User::whereNamespace($validated['receiver'])->first() ?? Organisation::whereNamespace($validated['receiver'])->first();

        // Lizenz erstellen
        $resource->createLicense($validated['license'], $receiver);

        $msg = sprintf('Herzlichen Glückwunsch, Sie haben eine Lizenz für "%s - %s" erworben!', $resource->title ?? $resource->name, $resource->full_namespace);
        $redirectUrl = Url::fromString($receiver->profileLicensesPath());
        $redirectUrl = $redirectUrl->withQueryParameter('fm', base64_encode($msg));

        return response()->json(['redirect_url' => $redirectUrl->__toString()]);
    }

    /**
     * Erstellt und speichert eine Lizenz für einen Prozess.
     * @param StoreSolutionLicense $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function storeSolutionLicense(StoreSolutionLicense $request) {
        /* @var Licensable|Model|Solution $model */
        /* @var Licensable $resource */
        /* @var User|Organisation $receiver */

        $validated = $request->validated();
        $resourceId = $validated['resource_id'];
        $resourceType = $validated['resource_type'];
        $processLicenses = $validated['process_licenses'];

        if (!class_exists($resourceType) || !new $resourceType() instanceof Licensable) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $model = $resourceType::make();
            $resource = $model::find($resourceId);
            $receiver = User::whereNamespace($validated['receiver'])->first() ?? Organisation::whereNamespace($validated['receiver'])->first();

            // Lizenz für Lösung nur erstellen, wenn der Empfänger diese noch nicht hat erstellen.
            if (!$receiver->hasLicense($resource)) {
                $resource->createLicense($validated['license'], $receiver);
            }

            foreach ($processLicenses as $licenseOptions) {
                $fullNamespace = $licenseOptions['full_namespace'];
                $options = $licenseOptions['license'];
                $process = Process::whereFullNamespace($fullNamespace)->firstOrFail();

                if (!$receiver->hasLicense($process)) {
                    $process->createLicense([
                        'level' => $options['level'],
                        'level_options' => $options['level_options'] ?? []
                    ], $receiver);
                }
            }

            DB::commit();
        }
        catch (Throwable $exception) {
            DB::rollBack();
            report($exception);

            return response()->json(['message' => $exception->getMessage()], 500);
        }

        $msg = sprintf('Herzlichen Glückwunsch, Sie haben Lizenzen für "%s - %s" erworben!', $resource->title ?? $resource->name, $resource->full_namespace);
        $redirectUrl = Url::fromString($receiver->profileLicensesPath());
        $redirectUrl = $redirectUrl->withQueryParameter('fm', base64_encode($msg));

        return response()->json(['redirect_url' => $redirectUrl->__toString()]);
    }

    /**
     * Gibt die vorhandenen Lizenzen für einen Benutzer und einer Lösung oder Prozess zurück.
     * @param string $ownerNamespace Benutzer/Organisation Namespace
     * @return JsonResponse
     */
    public function solutionLicense(string $ownerNamespace, Solution $solution) {
        /* @var User|Organisation $owner */
        $user = User::firstWhere('namespace', '=', $ownerNamespace);
        $owner = $user ?? Organisation::firstWhere('namespace', '=', $ownerNamespace);
        $license = License::identify($solution, $owner);

        $processIds = $solution->processes()->pluck('id');
        $processLicenses = License::whereOwnerId($owner->id)->whereIn('resource_id', $processIds)->get();

        return response()->json([
            'solution_license' => $license ? new SolutionLicense($license) : null,
            'process_licenses' => \App\Http\Resources\License::collection($processLicenses)
        ]);
    }
}

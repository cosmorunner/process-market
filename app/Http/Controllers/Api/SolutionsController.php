<?php

namespace App\Http\Controllers\Api;

use App\DemoConnector;
use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolution;
use App\Http\Requests\UpdateSolution;
use App\Http\Requests\UpdateSolutionConfig;
use App\Models\Demo;
use App\Models\Organisation;
use App\Models\Solution;
use App\Models\SolutionVersion;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class SolutionsController
 * @package App\Http\Controllers\Api
 */
class SolutionsController extends Controller {

    /**
     * Neue Solution anlegen.
     * @param StoreSolution $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(StoreSolution $request) {
        $validated = $request->validated();
        $namespace = $validated['namespace'];
        $identifier = $validated['identifier'];

        try {
            DB::beginTransaction();
            $solution = Solution::create([
                'name' => $validated['name'],
                'creator_id' => Auth::user()->id,
                'description' => $validated['description'],
                'namespace' => $namespace,
                'identifier' => $identifier,
                'visibility' => Visibility::Private->value,
                'license_options' => [
                    [
                        'level' => 'mixed',
                        'level_options' => []
                    ]
                ],
                'latest_version' => 'develop',
                'full_namespace' => $namespace . '/' . $identifier
            ]);

            // Tags hinzufügen, gegebenenfalls erstellen
            $tags = collect($validated['tags'] ?? []);
            $tagIds = $tags->map(fn($tag) => Tag::firstOrCreate(['name' => $tag], Tag::factory()->raw(['name' => trim($tag)])))
                ->pluck('id');
            $solution->tags()->sync($tagIds);

            /* @var SolutionVersion $version */
            $version = SolutionVersion::factory()->ofVersion('develop')->ofSolution($solution)->create();

            $solution->versions()->save($version);
            $solution->latestVersion()->associate($version);

            // Eigentümer setzen
            $owner = User::whereNamespace($namespace)->first() ?? Organisation::whereNamespace($namespace)->first();
            $solution->author()->associate($owner)->save();

            // Main Demo ohne Benutzer anlegen.
            $demo = Demo::createMainDemo($version);
            $connector = new DemoConnector($demo);
            $connector->instantiateDemo();

            DB::commit();
        }
        catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            if (isset($connector->token)) {
                $connector->deleteAllisaLiveDemo();
            }

            if (isset($demo)) {
                $demo->delete();
            }

            throw $exception;
        }

        return response()->json(['redirect' => $solution->configPath()], Response::HTTP_CREATED);
    }

    /**
     * Aktualisierung der Metadaten der Lösung.
     * @param UpdateSolution $request
     * @param Solution $solution
     * @return JsonResponse
     */
    public function update(UpdateSolution $request, Solution $solution) {
        $validated = $request->validated();

        // Tags hinzufügen, gegebenenfalls erstellen
        $tags = collect($validated['tags'] ?? []);
        $tagIds = $tags->map(fn($tag) => Tag::firstOrCreate(['name' => $tag], Tag::factory()->raw(['name' => trim($tag)])))
            ->pluck('id');

        $solution->tags()->sync($tagIds);

        $solution->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'visibility' => $validated['visibility'],
        ]);

        return response()->json(['redirect' => route('solution.edit', $solution)]);
    }

    /**
     * Aktualisierung der Konfiguration der Lösung.
     * @param UpdateSolutionConfig $request
     * @param Solution $solution
     * @return JsonResponse
     */
    public function updateConfig(UpdateSolutionConfig $request, Solution $solution) {
        $validated = $request->validated();

        $solution->update([
            'data' => $validated['data'],
        ]);

        return response()->json(['redirect' => route('solution.config', $solution)]);
    }

    /**
     * Returns the available versions of a solution.
     * @param string $identifier UUID or namespace (namespace/identifier)
     * @return JsonResponse
     */
    public function versions($identifier) {
        if (Uuid::isValid($identifier)) {
            $solution = Solution::find($identifier);
        }
        else {
            $namespace = explode('_', $identifier)[0];
            $identifier = explode('_', $identifier)[1];
            $solution = Solution::whereFullNamespace($namespace . '/' . $identifier)->first();
        }

        if (!$solution instanceof Solution) {
            return response()->json();
        }

        return response()->json(new SolutionVersion($solution));
    }
}

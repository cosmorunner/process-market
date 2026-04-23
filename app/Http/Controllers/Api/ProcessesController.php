<?php

namespace App\Http\Controllers\Api;

use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProcess;
use App\Http\Requests\UpdateProcess;
use App\Http\Resources\ProcessVersions;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class ProcessController
 * @package App\Http\Controllers\Api
 */
class ProcessesController extends Controller {

    /**
     * Neuen Prozess anlegen.
     * @param StoreProcess $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(StoreProcess $request) {
        $user = Auth::user();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $process = Process::create([
                'title' => $validated['title'],
                'creator_id' => $user->id,
                'description' => $validated['description'] ?? '',
                'namespace' => $validated['namespace'],
                'identifier' => $validated['identifier'],
                'visibility' => Visibility::Private->value,
                'license_options' => [
                    [
                        'level' => License::LEVEL_PRIVATE,
                        'level_options' => []
                    ]
                ],
                'latest_version' => 'develop',
                'full_namespace' => $validated['namespace'] . '/' . $validated['identifier']
            ]);

            // Tags hinzufügen, gegebenenfalls erstellen
            $tags = collect($validated['tags'] ?? []);
            $tagIds = $tags->map(fn($tag) => Tag::firstOrCreate(['name' => $tag], Tag::factory()->raw(['name' => trim($tag)])))
                ->pluck('id');
            $process->tags()->sync($tagIds);

            // Graphen hinzufügen (leer oder Template)
            /* @var Process $templateProcess */
            $template = (string) ($validated['template'] ?? '');

            if ($template && $templateProcess = Process::whereFullNamespace($template)->first()) {
                $processVersion = $templateProcess->latestPublishedVersion;

                if (!$processVersion instanceof ProcessVersion) {
                    $exception = new Exception(__('exceptions.default_message'));
                    report($exception);

                    throw $exception;
                }

                // Template-Id eintragen
                $process->template()->associate($processVersion);

                // Replicate process version
                $envs = $processVersion->environments;
                $processVersion = $processVersion->replicate(['published_at', 'changelog']);
                $processVersion->save();

                // Copy environments
                $processVersion->environments()->saveMany($envs);
            }
            else {
                $processVersion = $process->createInitialVersion();
            }

            $owner = User::whereNamespace($validated['namespace'])
                ->first() ?? Organisation::whereNamespace($validated['namespace'])->first();

            $process->author()->associate($owner)->save();
            $process->versions()->save($processVersion);
            $process->latestVersion()->associate($processVersion);
            $process->update(['latest_version' => 'develop']);

            // Set the name, namespace and identifier of the graph.
            $processVersion = $process->latestVersion;
            $definition = $processVersion->definition->toArray();
            $definition['version'] = 'develop';
            $definition['id'] = Uuid::uuid4()->toString();
            $definition['name'] = $validated['title'];
            $definition['namespace'] = $validated['namespace'];
            $definition['identifier'] = $validated['identifier'];

            $processVersion->definition = $definition;
            $processVersion->full_namespace = $process->full_namespace . '@develop';
            $processVersion->version = 'develop';
            $processVersion->save();

            // Update newly created process history item, since it holds the incorrect definition due to factory method blueprint (ProcessVersionObserver::created).
            $processVersion->historyHead->update(['definition' => $definition]);

            // Export process version as JSON file including dependency file.
            // By default, new process versions always have "develop" as their version number.
            $processVersion->exportDefinition();
            $processVersion->exportDependencies();

            DB::commit();

        }
        catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return response()->json(['redirect' => $process->devPath()], Response::HTTP_CREATED);
    }

    /**
     * Aktualisierung der Metadaten des Prozesses.
     * @param UpdateProcess $request
     * @param Process $process
     * @return JsonResponse
     */
    public function update(UpdateProcess $request, Process $process) {
        $validated = $request->validated();

        // Tags hinzufügen, gegebenenfalls erstellen
        $tags = collect($validated['tags'] ?? []);
        $tagIds = $tags->map(fn($tag) => Tag::firstOrCreate(['name' => $tag], Tag::factory()->raw(['name' => trim($tag)])))
            ->pluck('id');

        $process->tags()->sync($tagIds);

        $process->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $process->description,
            'visibility' => $validated['visibility'],
            'license_options' => $validated['license_options'] ?? []
        ]);

        return response()->json(['redirect' => route('process.edit', $process)]);
    }

    /**
     * Gibt die verfügbaren Versionen eines Prozesses zurück.
     * @param string $identifier UUID oder Full-Namespace (namespace/identifier)
     * @return JsonResponse
     */
    public function versions($identifier) {
        if (Uuid::isValid($identifier)) {
            $process = Process::find($identifier);
        }
        else {
            $namespace = explode('_', $identifier)[0];
            $identifier = explode('_', $identifier)[1];
            $process = Process::whereFullNamespace($namespace . '/' . $identifier)->first();
        }

        if (!$process instanceof Process) {
            return response()->json();
        }

        return response()->json(new ProcessVersions($process));
    }
}

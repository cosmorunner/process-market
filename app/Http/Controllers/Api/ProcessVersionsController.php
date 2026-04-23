<?php /** @noinspection PhpUnused */

namespace App\Http\Controllers\Api;

use App\AllisaConnector;
use App\Enums\Settings;
use App\Environment\Commands\Command as EnvironmentCommand;
use App\Exceptions\CommandDoesNotExist;
use App\Graph\Cytoscape;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyElement;
use App\Http\Requests\GetSyntaxValues;
use App\Http\Requests\PreviewTemplate;
use App\Http\Requests\StoreProcessVersion;
use App\Http\Requests\UpdateDefinition;
use App\Http\Requests\UpdateDemoData;
use App\Http\Requests\UpdateProcessVersionEnvironment;
use App\Http\Requests\UpdateProcessVersionEnvironmentBlueprint;
use App\Http\Requests\UpdateTemplatePreviewDataset;
use App\Http\Resources\Environment as EnvironmentResource;
use App\Http\Resources\ListSupportData;
use App\Loaders\PipeLoader;
use App\Models\Environment;
use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\Models\Setting;
use App\ProcessType\Commands\Command;
use App\ProcessType\Commands\Command as ProcessTypeCommand;
use App\ProcessType\ListConfig;
use App\ProcessType\Template;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * Class ProcessVersionsController
 * @package App\Http\Controllers\Api
 */
class ProcessVersionsController extends Controller {

    /**
     * Update the graph positions.
     * @param StoreProcessVersion $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(StoreProcessVersion $request, ProcessVersion $processVersion) {
        $validated = $request->validated();
        $calculated = $processVersion->calculated;
        $targetMap = collect($validated['targets'] ?? [])->mapWithKeys(fn($value, $key) => [
            $value['id'] => [
                'position' => $value['position'] ?? null,
                'classes' => $value['classes'] ?? null
            ]
        ])->toArray();

        foreach ($calculated as &$item) {
            $id = $item['data']['id'];

            if (isset($targetMap[$id]) && array_key_exists('position', $targetMap[$id]) && is_array($targetMap[$id]['position'])) {
                $item['position'] = $targetMap[$id]['position'];
            }

            if (isset($targetMap[$id]) && array_key_exists('classes', $targetMap[$id]) && is_string($targetMap[$id]['classes'])) {
                $item['classes'] = $targetMap[$id]['classes'];
            }
        }

        $processVersion->update(['calculated' => $calculated]);

        // Update The current history calculated attribute.
        if (!is_null($processVersion->historyHead)) {
            $processVersion->historyHead->update(['calculated' => $calculated]);
        }

        if ($processVersion->updated_by === auth()->user()->id) {
            return response()->json();
        }

        $processVersion->update(['updated_by' => auth()->user()->id]);

        return response()->json(['elements' => Cytoscape::renderHTMLElements($processVersion->definition, $calculated)]);
    }

    /**
     * Update demo data.
     * @param UpdateDemoData $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     */
    public function updateDemoData(UpdateDemoData $request, ProcessVersion $processVersion) {
        $validated = $request->validated();

        $processVersion->update([
            'demo_data' => $validated['demo_data'],
        ]);

        return response()->json($validated['demo_data']);
    }

    /**
     * Update definition.
     * @param UpdateDefinition $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     * @throws Throwable
     */
    public function updateDefinition(UpdateDefinition $request, ProcessVersion $processVersion) {
        $currentCalculated = $processVersion->calculated;
        $validated = $request->validated();
        $viewPort = $validated['view_port'] ?? [];
        $position = $validated['position'] ?? null;

        $user = auth()->user();

        try {
            $commandName = $validated['command'] ?? '';
            $commandPayload = $validated['payload'] ?? null;
            $command = ProcessTypeCommand::create($commandName, $commandPayload, $processVersion->definition, $processVersion);

            // In the event that no "position" has been specified, it is left to the command to be executed,
            // to determine a position if necessary. For example, when creating a new action via the "Add" button
            // the new action should be positioned at the "Free actions" node.
            $position = json_decode($position ?? json_encode($command->preferredGraphPosition()), true);
        }
        catch (CommandDoesNotExist $exception) {
            return response()->json(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Execute command
        $updatedDefinition = $command->execute();

        // Use the executed commands to determine which parts of the definition must be returned in the JSON response.
        // This is determined so that the complete definition does not have to be returned after every command.
        /* @var Command $innerCommand */
        $affectedDefinitionParts = collect($command->executedCommands)
            ->map(fn($innerCommand) => $innerCommand::AFFECTS_PARTS)
            ->flatten()
            ->unique()
            ->toArray();

        // The graph must be recalculated for certain changes (new action, new rule, delete rule, etc.).
        if ($command->recalculate) {
            $newCalculated = (new Cytoscape($updatedDefinition))->transform();
            $oldCalulated = $currentCalculated;
            $currentCalculated = Cytoscape::applyOldPositions($newCalculated, $oldCalulated);
            $currentCalculated = Cytoscape::applyOldClasses($currentCalculated, $oldCalulated);
            $currentCalculated = Cytoscape::setEmptyPositions($currentCalculated, $viewPort);
            $currentCalculated = $command->updateGraphPositions($position, $currentCalculated);
        }

        $asArray = $updatedDefinition->toArray();

        $processVersion->updateQuietly([
            'definition' => $asArray,
            'calculated' => $currentCalculated,
            'updated_by' => $user->id,
        ]);

        // After the definition was updated, we call the "afterCommandExecution" command callback,
        // to manage any remaining logic of the command.
        $command->afterCommandExecution($processVersion, $commandPayload);

        // Update definition dependencies
        $processVersion->updateDependencies();

        // Enter in the process that it has been updated.
        $processVersion->process->touch();

        // Delete all history younger than the current head,
        // in case the user undo commands and after that does a new command.
        $processVersion->succeedingHistory()->delete();

        // Save process version in the history after execute the command.
        $history = ProcessVersionHistory::makeWithCommand($processVersion, $commandName, $commandPayload);
        $processVersion->history()->save($history);

        // Update process version history head to the latest history.
        $processVersion->update(['history_head' => $history->id]);

        // Keep only the latest 10 history for the process version.
        $historyItems = $processVersion->history()->latest()->get();

        $itemsToDelete = $historyItems->skip(config('app.process_version_history_length', 10));
        $idsToDelete = $itemsToDelete->pluck('id')->toArray();
        ProcessVersionHistory::destroy($idsToDelete);

        // Only certain areas are returned if $affectedDefinitionParts is not empty.
        if (count($affectedDefinitionParts)) {
            $asArray = array_intersect_key($asArray, array_flip($affectedDefinitionParts));
        }

        // The graph elements do not have to be returned when changes are made in the configuration area.
        if ($request->query('only-definition')) {
            return response()->json(['definition' => $asArray]);
        }

        return response()->json([
            'definition' => $asArray,
            'elements' => Cytoscape::renderHTMLElements($updatedDefinition, $currentCalculated),
        ]);
    }

    /**
     * Returns the complete definition of the graph.
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     */
    public function definition(ProcessVersion $processVersion) {
        return response()->json($processVersion->definition->toArray());
    }

    /**
     * Returns data on process types, the status types and process data required for the
     * configuration of the lists.
     * @param ProcessVersion $processVersion
     * @param string $listConfigId
     * @return JsonResponse
     */
    public function listSupportData(ProcessVersion $processVersion, string $listConfigId) {
        $listConfig = $processVersion->definition->listConfig($listConfigId);

        // Only return a specific part of the list support data for this list config
        $parts = request()->query('parts', []);
        $parts = is_string($parts) ? array_map('trim', explode(',', $parts)) : $parts;

        if (!$listConfig instanceof ListConfig) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(new ListSupportData($listConfig, $processVersion, $parts));
    }

    /**
     * Create a new simulation environment.
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     */
    public function storeEnvironment(ProcessVersion $processVersion) {
        $environment = Environment::factory()->emptyWithName('Standard')->make()->toArray();

        // If this is the first environment for the graph, it becomes the “default” environment.
        if ($processVersion->environments->isEmpty()) {
            $environment['default'] = true;
        }

        $environment = $processVersion->environments()->create($environment);

        return response()->json(new EnvironmentResource($environment));
    }

    /**
     * Update a simulation environment.
     * @param UpdateProcessVersionEnvironment $request
     * @param ProcessVersion $processVersion
     * @param Environment $environment
     * @return JsonResponse
     */
    public function updateEnvironment(UpdateProcessVersionEnvironment $request, ProcessVersion $processVersion, Environment $environment) {
        $validated = $request->validated();
        $default = $validated['default'];

        // Reset default environment
        if ($default) {
            Environment::where('default', '=', true)
                ->where('process_version_id', '=', $processVersion->id)
                ->where('id', '!=', $environment->id)
                ->update(['default' => false]);
        }

        $environment->update($validated);
        $processVersion->updateDependencies();

        return response()->json(EnvironmentResource::collection($processVersion->environments));
    }

    /**
     * Copy a simulation environment.
     * @param ProcessVersion $processVersion
     * @param Environment $environment
     * @return JsonResponse
     */
    public function copyEnvironment(ProcessVersion $processVersion, Environment $environment) {
        /* @var Environment $newEnvironment */
        $newEnvironment = Environment::factory()->create(array_merge($environment->attributesToArray(), [
            'id' => null,
            'default' => false,
            'name' => $environment->name . ' - Kopie'
        ]));

        $processVersion->updateDependencies();

        return response()->json(new EnvironmentResource($newEnvironment));
    }

    /**
     * Updates the blueprint of a simulation environment.
     * @param UpdateProcessVersionEnvironmentBlueprint $request
     * @param ProcessVersion $processVersion
     * @param Environment $environment
     * @return JsonResponse
     */
    public function updateEnvironmentBlueprint(UpdateProcessVersionEnvironmentBlueprint $request, ProcessVersion $processVersion, Environment $environment) {
        try {
            $commandName = $request->validated()['command'] ?? '';
            $commandPayload = $request->validated()['payload'] ?? null;
            $command = EnvironmentCommand::create($commandName, $commandPayload, $environment);
        }
        catch (CommandDoesNotExist $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        $environment = $command->execute();
        $environment->update(['blueprint' => $environment->blueprint->toArray()]);

        // Check if the process of the environment "query_context" still exists and remove it if not.
        $processId = PipeLoader::getKey($environment->query_context);
        $blueprintProcessIds = $environment->blueprint->processes->pluck('id');
        $blueprintUserIds = $environment->blueprint->users->pluck('id');

        // Remove query context, if the process was deleted
        if (Uuid::isValid($processId) && !$blueprintProcessIds->contains($processId)) {
            $environment->update(['query_context' => '']);
        }

        // Reset "default_user" when the user was deleted.
        if (is_string($environment->default_user) && Uuid::isValid($environment->default_user) && !$blueprintUserIds->contains($environment->default_user)) {
            $environment->update(['default_user' => null]);
        }

        // When updating the blueprint, the dependencies are updated if necessary, which is why the definition is saved here.
        // definition is stored here.
        $processVersion->updateDependencies();

        return response()->json(new EnvironmentResource($environment->refresh()));
    }


    /**
     * Delete a simulation environment.
     * @param ProcessVersion $processVersion
     * @param Environment $environment
     * @return JsonResponse
     */
    public function deleteEnvironment(ProcessVersion $processVersion, Environment $environment) {
        try {
            $environment->delete();
            $processVersion->updateDependencies();
        }
        catch (Exception) {
            // Ignore
        }

        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * Returns the syntax and pipeloader values available to the user.
     * @param GetSyntaxValues $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     */
    public function syntaxValues(GetSyntaxValues $request, ProcessVersion $processVersion) {
        $validated = $request->validated();
        $search = $validated['search'] ?? null;
        $actionType = null;

        if (!empty($validated['action_type_id'])) {
            $actionType = $processVersion->definition->actionType($validated['action_type_id']);
        }

        $parts = [
            'syntax_parts' => $validated['syntax_parts'] ?? [],
            'pipe_parts' => $validated['pipe_parts'] ?? [],
        ];

        return response()->json($processVersion->syntaxValues($parts, $actionType, $search));
    }

    /**
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     * @throws Throwable
     */
    public function undo(ProcessVersion $processVersion) {
        $head = $processVersion->historyHead;
        // There is no history.
        if (is_null($head)) {
            return response()->json([
                'definition' => $processVersion->definition->toArray(),
                'elements' => Cytoscape::renderHTMLElements($processVersion->definition, $processVersion->calculated),
                'undo' => false,
                'redo' => false
            ]);
        }

        // We get the last to history items to know if there is still one left before the previous one.
        $previousItems = $processVersion->history()->where('created_at', '<', $head->created_at)->latest()->limit(2)->get();

        /* @var ProcessVersionHistory $previous */
        $previous = $previousItems->first();

        // The head is the first record in the history (the earliest created/no more history items to jump back to).
        if (is_null($previous)) {
            return response()->json([
                'definition' => $processVersion->definition->toArray(),
                'elements' => Cytoscape::renderHTMLElements($processVersion->definition, $processVersion->calculated),
                'undo' => false,
                'redo' => true
            ]);
        }

        // Update process version history to the previous history.
        $processVersion->update([
            'history_head' => $previous->id,
            'definition' => $previous->getRawDefintion(),
            'calculated' => $previous->calculated
        ]);

        // By checking if two were taken, we know there is still an entry left, so we keep undo to true.
        $undo = $previousItems->count() === 2;

        return response()->json([
            'definition' => $previous->getRawDefintion(),
            'elements' => Cytoscape::renderHTMLElements($processVersion->definition, $previous->calculated),
            'undo' => $undo,
            'redo' => true
        ]);
    }

    /**
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     * @throws Throwable
     */
    public function redo(ProcessVersion $processVersion) {
        $head = $processVersion->historyHead;
        // There is no history.
        if (is_null($head)) {
            return response()->json([
                'definition' => $processVersion->definition->toArray(),
                'elements' => Cytoscape::renderHTMLElements($processVersion->definition, $processVersion->calculated),
                'undo' => false,
                'redo' => false
            ]);
        }

        // We get the oldest two history items to know if there is still one left before the previous one.
        $nextItems = $processVersion->history()->where('created_at', '>', $head->created_at)->oldest()->limit(2)->get();

        /* @var ProcessVersionHistory $next */
        $next = $nextItems->first();

        // The head is the last record in the history (the latest created, head is in first position/front)
        if (is_null($next)) {
            return response()->json([
                'definition' => $processVersion->definition->toArray(),
                'elements' => Cytoscape::renderHTMLElements($processVersion->definition, $processVersion->calculated),
                'undo' => true,
                'redo' => false
            ]);
        }

        // Update process version history to the next history.
        $processVersion->update([
            'history_head' => $next->id,
            'definition' => $next->getRawDefintion(),
            'calculated' => $next->calculated
        ]);

        // By checking if two were taken, we know there is still a enty left, so we keep undo to true.
        $redo = $nextItems->count() === 2;

        return response()->json([
            'definition' => $next->getRawDefintion(),
            'elements' => Cytoscape::renderHTMLElements($processVersion->definition, $next->calculated),
            'undo' => true,
            'redo' => $redo
        ]);
    }

    /**
     * @param CopyElement $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     */
    public function copyElement(CopyElement $request, ProcessVersion $processVersion) {
        $validated = $request->validated();

        $data = [
            'namespace' => $processVersion->namespace,
            'name' => $validated['name'],
            'object' => $validated['object']
        ];

        Setting::upsertSettingForUser('copy_element', $data);

        return response()->json(['namespace' => $processVersion->namespace]);
    }

    /**
     * @param Request $request
     * @param ProcessVersion $processVersion
     * @return JsonResponse
     * @noinspection PhpUnusedParameterInspection
     */
    public function deleteCopyElement(Request $request, ProcessVersion $processVersion) {
        Setting::deleteUserSetting('copy_element');

        return response()->json();
    }

    /**
     * Returns the preview data for a specific template
     * @param ProcessVersion $processVersion
     * @param string $templateId
     * @return JsonResponse
     */
    public function previewDatasets(ProcessVersion $processVersion, string $templateId) {
        $allTemplates = Setting::retrieve(Settings::TemplatePreviewDatasets->value, [], $processVersion);

        return response()->json($allTemplates[$templateId] ?? []);
    }

    /**
     * Update a specific dataset of a process type template
     * @param UpdateTemplatePreviewDataset $request
     * @param ProcessVersion $processVersion
     * @param string $templateId
     * @param string $datasetId
     * @return void
     */
    public function updatePreviewDataset(UpdateTemplatePreviewDataset $request, ProcessVersion $processVersion, string $templateId, string $datasetId) {
        $validated = $request->validated();
        $allTemplates = Setting::retrieve(Settings::TemplatePreviewDatasets->value, [], $processVersion);

        $templateDatasets = collect($allTemplates[$templateId] ?? [])->map(function ($dataset) use ($datasetId, $validated) {
            if ($dataset['id'] ?? '' === $datasetId) {
                $dataset['values'] = $validated['values'] ?? [];
            }

            return $dataset;
        })->toArray();

        $allTemplates[$templateId] = $templateDatasets;

        Setting::upsertSetting(Settings::TemplatePreviewDatasets->value, $allTemplates, $processVersion);
    }

    /**
     * Preview a process type template.
     * @return JsonResponse|Response
     */
    public function previewTemplate(PreviewTemplate $request, ProcessVersion $processVersion, string $templateId) {
        $template = $processVersion->definition->template($templateId);

        if (!$template instanceof Template) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();
        $data = $validated['data'] ?? [];
        $templateBase64 = $validated['template'] ?? '';
        $output = $validated['options']['output'] ?? 'html';
        $connector = new AllisaConnector();

        if ($template->type === 'html') {
            $options = [
                'output' => $validated['options']['output'] ?? 'html',
                'base64_encoded' => $output === 'pdf'
            ];
        }
        else {
            $options = [];
        }

        return $connector->previewTemplate($templateBase64, $template->type, $data, $options);
    }

}

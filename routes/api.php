<?php

use App\Http\Controllers\Api\ExploreController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\LicensesController;
use App\Http\Controllers\Api\OrganisationsController;
use App\Http\Controllers\Api\ProcessesController;
use App\Http\Controllers\Api\ProcessVersionsController;
use App\Http\Controllers\Api\SimulationsController;
use App\Http\Controllers\Api\SolutionsController;
use App\Http\Controllers\Api\SolutionVersionsController;
use App\Http\Controllers\Api\TagsController;
use App\Http\Controllers\Api\TemplatesController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

Route::get('/explore', [ExploreController::class, 'index'])->name('api.explore.index')->middleware('throttle:60,1');

Route::group(['middleware' => 'auth:api'], function() {

    // Prozess
    Route::post('/processes', [ProcessesController::class, 'store'])->name('api.process.store');
    Route::patch('/processes/{process}', [ProcessesController::class, 'update'])
        ->name('api.process.update')
        ->middleware('can:update,process');

    // Solution
    Route::post('/solutions', [SolutionsController::class, 'store'])->name('api.solution.store');
    Route::patch('/solutions/{solution}', [SolutionsController::class, 'update'])->name('api.solution.update')->middleware('can:update,solution');
    Route::patch('/solutions/{solution}/config', [SolutionsController::class, 'updateConfig'])->name('api.solution.update_config')->middleware('can:update,solution');

    // Solution Version
    Route::patch('/solution-version/{solutionVersion}', [SolutionVersionsController::class, 'update'])->name('api.solution_version.update')->middleware('can:update,solutionVersion');

    // Tags
    Route::get('/tags', [TagsController::class, 'index'])->name('api.tags');

    // Prozess-Version
    Route::get('/process-versions/{processVersion}/definition', [ProcessVersionsController::class, 'definition'])
        ->name('api.process_version.definition')
        ->middleware('can:view,processVersion');
    Route::get('/process-versions/{processVersion}/listconfigs/{listConfigId}/list-support/', [
        ProcessVersionsController::class,
        'listSupportData'
    ])->name('api.process_version.list_support_data')->middleware('can:view,processVersion');
    Route::get('/process-versions/{processVersion}/templates/{templateId}/preview-datasets', [ProcessVersionsController::class, 'previewDatasets'])->name('api.process_version.templates.preview_datasets')->middleware('can:update,processVersion');
    Route::patch('/process-versions/{processVersion}/templates/{templateId}/preview-dataset/{datasetId}', [ProcessVersionsController::class, 'updatePreviewDataset'])->name('api.process_version.templates.update_preview_dataset')->middleware('can:update,processVersion')->withoutMiddleware(ConvertEmptyStringsToNull::class);
    Route::post('/process-versions/{processVersion}/templates/{templateId}/preview', [ProcessVersionsController::class, 'previewTemplate'])->name('api.process_version.templates.preview')->middleware('can:update,processVersion');
    Route::patch('/process-versions/{processVersion}', [ProcessVersionsController::class, 'update'])->name('api.process_version.update')->middleware('can:update,processVersion');
    Route::patch('/process-versions/{processVersion}/demo-data', [ProcessVersionsController::class, 'updateDemoData'])->name('api.process_version.update_demo_data')->middleware('can:view,processVersion');
    Route::post('/process-versions/{processVersion}/environments', [ProcessVersionsController::class, 'storeEnvironment'])->name('api.process_version.store_environment')->middleware('can:createEnvironment,processVersion');
    Route::post('/process-versions/{processVersion}/syntax-values', [ProcessVersionsController::class, 'syntaxValues'])->name('api.process_version.syntax_values')->middleware('can:view,processVersion');
    Route::patch('/process-versions/{processVersion}/definition', [ProcessVersionsController::class, 'updateDefinition'])->name('api.process_version.update_definition')->middleware('can:update,processVersion')->withoutMiddleware(ConvertEmptyStringsToNull::class);
    Route::patch('/process-versions/{processVersion}/environments/{environment}/blueprint', [ProcessVersionsController::class, 'updateEnvironmentBlueprint'])->name('api.process_version.update_environment_blueprint')->middleware('can:update,environment');
    Route::patch('/process-versions/{processVersion}/environments/{environment}', [ProcessVersionsController::class, 'updateEnvironment'])->name('api.process_version.update_environment')->middleware('can:update,environment');
    Route::post('/process-versions/{processVersion}/environments/{environment}/copy', [ProcessVersionsController::class, 'copyEnvironment'])->name('api.process_version.copy_environment')->middleware('can:update,environment');
    Route::delete('/process-versions/{processVersion}/environments/{environment}', [ProcessVersionsController::class, 'deleteEnvironment'])->name('api.process_version.delete_environment')->middleware('can:delete,environment');
    Route::patch('/process-versions/{processVersion}/undo', [ProcessVersionsController::class, 'undo'])->name('api.process_version.undo')->middleware('can:update,processVersion');
    Route::patch('/process-versions/{processVersion}/redo', [ProcessVersionsController::class, 'redo'])->name('api.process_version.redo')->middleware('can:update,processVersion');
    Route::patch('/process-versions/{processVersion}/copy-element', [ProcessVersionsController::class, 'copyElement'])->name('api.process_version.copy_element')->middleware('can:update,processVersion');
    Route::delete('/process-versions/{processVersion}/copy-element', [ProcessVersionsController::class, 'deleteCopyElement'])->name('api.process_version.delete_copy_element')->middleware('can:update,processVersion');

    // Simulation starten
    Route::post('/simulations/process-version/{processVersion}', [SimulationsController::class, 'store'])->name('api.simulation.store')->middleware('can:create,App\Models\Simulation,processVersion');

    // Simulation laden
    Route::get('/simulations/{simulation}', [SimulationsController::class, 'show'])->name('api.simulation.show')->middleware('can:update,simulation');

    // Benutzer der Simulation laden
    Route::get('/simulations/{simulation}/users', [SimulationsController::class, 'users'])->name('api.simulation.users')->middleware('can:update,simulation');
    Route::get('/simulations/{simulation}/sync-allisa-id', [SimulationsController::class, 'syncAllisaId'])->name('api.simulation.sync_allisa_id')->middleware('can:update,simulation');

    // Aktionstyp-Inputwerte laden
    Route::get('/simulations/{simulation}/actiontypes/{actionTypeId}', [SimulationsController::class, 'actiontype'])->name('api.simulation.actiontype')->middleware('can:update,simulation');

    // Systemliste laden
    Route::get('/simulations/{simulation}/lists/{slug}', [SimulationsController::class, 'list'])->name('api.simulation.list')->middleware('can:update,simulation');

    // Simulation-Benutzer wechseln
    Route::patch('/simulations/{simulation}/switch-user', [SimulationsController::class, 'switchUser'])->name('api.simulation.switch_user')->middleware('can:update,simulation');

    // Simulation beenden
    Route::patch('/simulations/{simulation}', [SimulationsController::class, 'finish'])->name('api.simulation.finish')->middleware('can:update,simulation');

    // Aktion in Simulation ausführen
    Route::post('/simulations/{simulation}/actions', [SimulationsController::class, 'execute'])->name('api.simulation.execute_action')->middleware('can:update,simulation');

    // Aktion in Simulation rückgängig machen
    Route::delete('/simulations/{simulation}/actions/{action}', [SimulationsController::class, 'undo'])->name('api.simulation.undo')->middleware('can:update,simulation');

    // Benutzer-Einstellung aktualisieren
    Route::patch('/user/settings', [UsersController::class, 'updateSettings'])->name('api.user.settings.update');
    Route::patch('/user/settings/sidebar', [UsersController::class, 'toggleSidebar'])->name('api.user.settings.toggle_sidebar');

    // Profilbild des Users ändern
    Route::patch('/user/profile-picture', [UsersController::class, 'profilePicture'])->name('api.user.profile_picture');

    // Prozess-Versionen des Benutzers holen
    Route::get('/user/process-versions/{meta?}', [UsersController::class, 'processVersions'])->name('api.user.process_versions');

    // Process-Versionen einer Organisation holen
    Route::get('/organisations/{organisation}/process-versions/{meta?}', [OrganisationsController::class, 'processVersions'])->name('api.organisation.process_versions')->middleware('can:viewProfile,organisation');

    // Profilbild einer Organisation ändern
    Route::patch('/organisations/{organisation}/profile-picture', [OrganisationsController::class, 'profilePicture'])->name('api.organisation.profile_picture')->middleware('can:manageAccount,organisation');

    // Templates
    Route::get('/templates', [TemplatesController::class, 'index'])->name('api.templates');

    // Prüfen ob eine Allisa Plattform existiert
    Route::get('/allisa-platform-exists/{identifier}', [IndexController::class, 'allisaPlatformExists'])->name('api.allisa_platform_exists')->middleware('throttle:30,1');
    Route::post('/store-allisa-platform', [IndexController::class, 'storeAllisaPlatform'])->name('api.store_allisa_platform')->middleware(['throttle:10,1', 'allisa.console.enabled']);

    // Lizenzen
    Route::post('/licenses/process', [LicensesController::class, 'storeProcessLicense'])->name('api.licenses.store_process_license')->middleware('can:create,App\Models\License');
    Route::post('/licenses/solution', [LicensesController::class, 'storeSolutionLicense'])->name('api.licenses.store_solution_license')->middleware('can:create,App\Models\License');
    Route::get('/licenses/{ownerNamespace}/solutions/{solution}', [LicensesController::class, 'solutionLicense'])->name('api.licenses.solution')->middleware('can:view_licenses,solution,ownerNamespace');
});

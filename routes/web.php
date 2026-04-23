<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DemosController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LicensesController;
use App\Http\Controllers\OrganisationsController;
use App\Http\Controllers\ProcessesController;
use App\Http\Controllers\ProcessVersionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SimulationsController;
use App\Http\Controllers\SolutionsController;
use App\Http\Controllers\SolutionVersionsController;
use App\Http\Controllers\UsersController;

/**
 * Authentifizierte Routen.
 */
Route::group(['middleware' => 'auth'], function() {

    // Changelog (only Admins).
    Route::get('/changelog', [IndexController::class, 'changelog'])->name('changelog');

    // Usercontext
    Route::patch('/update-user-context', [IndexController::class, 'updateUserContext'])->name('update_user_context');

    // Prozesse
    Route::get('processes', [ProcessesController::class, 'index'])->name('processes');
    Route::get('processes/create', [ProcessesController::class, 'create'])->name('process.create');
    Route::get('processes/{process}/purchase', [ProcessesController::class, 'purchase'])->name('process.purchase');
    Route::get('processes/{process}/demo/{version}', [ProcessesController::class, 'demo'])->name('process.demo')->middleware('can:start_demo,process,version');
    Route::get('processes/{process}/start-demo/{version}', [ProcessesController::class, 'startDemo'])->name('process.start_demo')->middleware('can:start_demo,process');
    Route::get('processes/{process}/sync/{version}', [ProcessesController::class, 'showSync'])->name('process.sync')->middleware('can:sync,process');
    Route::get('processes/{process}/complete', [ProcessesController::class, 'complete'])->name('process.complete')->middleware('can:complete,process');
    Route::get('processes/{process}/edit', [ProcessesController::class, 'edit'])
        ->name('process.edit')
        ->middleware('can:edit,process');
    Route::get('processes/{process}/versions', [ProcessesController::class, 'versions'])->name('process.versions')->middleware('can:view,process');
    Route::get('processes/{process}/delete', [ProcessesController::class, 'delete'])->name('process.delete')->middleware('can:delete,process');
    Route::get('processes/{process}/archive', [ProcessesController::class, 'archive'])->name('process.archive')->middleware('can:delete,process');
    Route::get('processes/{process}/restore', [ProcessesController::class, 'restore'])
        ->name('process.restore')->middleware('can:restore,process');
    Route::patch('processes/{process}/restore/execute', [ProcessesController::class, 'executeRestore'])
        ->name('process.restore.execute')->middleware('can:restore,process');
    Route::get('processes/{process}/develop/{version?}', [ProcessesController::class, 'develop'])->name('process.develop')->middleware('can:view,process');
    Route::get('processes/{process}/config/{version?}', [ProcessesController::class, 'config'])->name('process.config')->middleware('can:view,process');
    Route::delete('processes/{process}', [ProcessesController::class, 'destroy'])->name('process.destroy')->middleware('can:delete,process');

    // Prozess Detailansicht - Demo
    Route::get('process/{namespace}/{identifier}/demo/{version}', [ProcessesController::class, 'publicDemo'])->name('process.detail.demo');

    // Prozess Detailansicht - Demo starten
    Route::get('process/{namespace}/{identifier}/start-demo/{version}', [ProcessesController::class, 'publicStartDemo'])->name('process.detail.start_demo');

    // Solutions
    Route::get('solutions', [SolutionsController::class, 'index'])->name('solutions');
    Route::get('solutions/create', [SolutionsController::class, 'create'])->name('solution.create');
    Route::get('solutions/{solution}/show', [SolutionsController::class, 'show'])->name('solution.show')->middleware('can:start_demo,solution');
    Route::get('solutions/{solution}/purchase/{version}', [SolutionsController::class, 'purchase'])->name('solution.purchase');
    Route::get('solutions/{solution}/demo/{version}', [SolutionsController::class, 'demo'])->name('solution.demo')->middleware('can:start_demo,solution,version');
    Route::get('solutions/{solution}/start-demo/{version}', [SolutionsController::class, 'startDemo'])->name('solution.start_demo')->middleware('can:start_demo,solution');
    Route::get('solutions/{solution}/sync/{version}', [SolutionsController::class, 'showSync'])->name('solution.sync')->middleware('can:sync,solution');
    Route::get('solutions/{solution}/complete', [SolutionsController::class, 'complete'])->name('solution.complete')->middleware('can:complete,solution');
    Route::get('solutions/{solution}/config', [SolutionsController::class, 'config'])->name('solution.config')->middleware('can:view,solution');
    Route::get('solutions/{solution}/edit', [SolutionsController::class, 'edit'])->name('solution.edit')->middleware('can:update,solution');
    Route::get('solutions/{solution}/versions', [SolutionsController::class, 'versions'])->name('solution.versions')->middleware('can:view,solution');
    Route::get('solutions/{solution}/delete', [SolutionsController::class, 'delete'])->name('solution.delete')->middleware('can:delete,solution');
    Route::get('solutions/{solution}/archive', [SolutionsController::class, 'archive'])->name('solution.archive')->middleware('can:delete,solution');
    Route::delete('solutions/{solution}', [SolutionsController::class, 'destroy'])->name('solution.destroy')->middleware('can:delete,solution');

    // Lösung Detailansicht - Demo
    Route::get('solution/{namespace}/{identifier}/demo/{version}', [SolutionsController::class, 'publicDemo'])->name('solution.detail.demo');

    // Lösung Detailansicht - Demo starten
    Route::get('solution/{namespace}/{identifier}/start-demo/{version}', [SolutionsController::class, 'publicStartDemo'])->name('solution.detail.start_demo');

    // Solution Version
    Route::get('solution-versions/{solutionVersion}/sync', [SolutionVersionsController::class, 'sync'])->name('solution_version.sync')->middleware('can:sync,solutionVersion');
    Route::patch('solution-versions/{solutionVersion}/publish', [SolutionVersionsController::class, 'publish'])->name('solution_version.publish')->middleware('can:complete,solutionVersion');
    Route::post('solution-version/{solutionVersion}/syncs', [SolutionVersionsController::class, 'executeSync'])->name('solution_version.execute_sync')->middleware('can:sync,solutionVersion');

    // Lizenzen
    Route::get('licenses/{license}/demo/{version}', [LicensesController::class, 'demo'])->name('license.demo')->middleware('can:start_demo,license,version');
    Route::get('licenses/{license}/start-process-demo/{version}', [LicensesController::class, 'startProcessDemo'])->name('license.start_process_demo')->middleware('can:start_demo,license,version');
    Route::get('licenses/{license}/start-solution-demo/{version}', [LicensesController::class, 'startSolutionDemo'])->name('license.start_solution_demo')->middleware('can:start_demo,license,version');
    Route::get('licenses/{license}/versions', [LicensesController::class, 'versions'])->name('license.versions')->middleware('can:view,license');
    Route::get('licenses/{license}/sync/{version}', [LicensesController::class, 'showSync'])->name('license.sync')->middleware('can:sync,license');
    Route::post('licenses/{license}/syncs/{version}', [LicensesController::class, 'executeSync'])->name('license.execute_sync')->middleware('can:sync,license');
    Route::post('licenses/{license}/process-demo/{version}', [LicensesController::class, 'storeProcessDemo'])->name('license.store_process_demo')->middleware('can:start_demo,license,version');

    // Prozess-Versions
    Route::get('process-versions/{processVersion}/sync', [ProcessVersionsController::class, 'sync'])->name('process_version.sync')->middleware('can:sync,processVersion');
    Route::get('process-versions/{processVersion}/restore', [ProcessVersionsController::class, 'restore'])->name('process_version.restore')->middleware('can:restore,processVersion');
    Route::patch('process-versions/{processVersion}/publish', [ProcessVersionsController::class, 'publish'])->name('process_version.publish')->middleware('can:complete,processVersion');
    Route::post('process-versions/{processVersion}/syncs', [ProcessVersionsController::class, 'executeSync'])->name('process_version.execute_sync')->middleware('can:sync,processVersion');
    Route::post('process-versions/{processVersion}/rollback', [ProcessVersionsController::class, 'rollback'])->name('process_version.rollback')->middleware('can:restore,processVersion');
    Route::get('process-versions/{processVersion}/download', [ProcessVersionsController::class, 'download'])->name('process_version.download')->middleware('can:download,processVersion');

    // Account Settings
    Route::get('manage/setting', [SettingsController::class, 'index'])->name('settings');
    Route::get('manage/update-settings/{settingName}', [SettingsController::class, 'updateSetting'])->name('settings.update');
    Route::get('manage/setting/data', [SettingsController::class, 'data'])->name('settings.data');
    Route::get('manage/setting/organisations', [SettingsController::class, 'organisations'])->name('settings.organisations');
    Route::get('manage/setting/systems', [SettingsController::class, 'systems'])->name('settings.systems');
    Route::get('manage/setting/systems/create', [SettingsController::class, 'createSystem'])->name('settings.system.create');
    Route::get('manage/setting/security', [SettingsController::class, 'security'])->name('settings.security');
    Route::get('manage/setting/account', [SettingsController::class, 'account'])->name('settings.account');
    Route::patch('manage/setting/update-password', [SettingsController::class, 'updatePassword'])->name('settings.update_password');
    Route::patch('manage/setting/update-data', [SettingsController::class, 'updateData'])->name('settings.update_data');
    Route::post('manage/setting/systems', [SettingsController::class, 'storeSystem'])->name('settings.system.store');
    Route::delete('manage/setting/systems/{system}', [SettingsController::class, 'deleteSystem'])->name('settings.system.delete')->middleware('can:delete,system');

    // Eigenes Profil
    Route::get('manage/profile/processes', [ProfileController::class, 'processes'])->name('profile.processes');
    Route::get('manage/profile/solutions', [ProfileController::class, 'solutions'])->name('profile.solutions');
    Route::get('manage/profile/licenses', [ProfileController::class, 'licenses'])->name('profile.licenses');
    Route::get('manage/profile/create-allisa-demo', [ProfileController::class, 'createAllisaDemo'])->name('profile.create_allisa_demo')->middleware('allisa.console.enabled');
    Route::delete('manage/profile', [ProfileController::class, 'closeAccount'])->name('profile.close');

    // Organisation
    Route::get('manage/organisations/create', [OrganisationsController::class, 'create'])->name('organisation.create');
    Route::get('manage/organisations/{organisation}/processes', [OrganisationsController::class, 'processes'])->name('organisation.processes')->middleware('can:viewProfile,organisation');
    Route::get('manage/organisations/{organisation}/solutions', [OrganisationsController::class, 'solutions'])->name('organisation.solutions')->middleware('can:viewProfile,organisation');
    Route::get('manage/organisations/{organisation}/licenses', [OrganisationsController::class, 'licenses'])->name('organisation.licenses')->middleware('can:viewLicenses,organisation');
    Route::get('manage/organisations/{organisation}/invite', [OrganisationsController::class, 'inviteMember'])->name('organisation.members.invite')->middleware('can:manageMembers,organisation');
    Route::get('manage/organisations/{organisation}/members', [OrganisationsController::class, 'members'])->name('organisation.members')->middleware('can:viewMembers,organisation');
    Route::get('manage/organisations/{organisation}/members/{user}', [OrganisationsController::class, 'editMember'])->name('organisation.members.edit')->middleware(['can:manageMembers,organisation', 'can:manageMember,organisation,user']);
    Route::get('manage/organisations/{organisation}/settings/data', [OrganisationsController::class, 'data'])->name('organisation.settings.data')->middleware('can:manageAccount,organisation');
    Route::get('manage/organisations/{organisation}/settings/account', [OrganisationsController::class, 'account'])->name('organisation.settings.account')->middleware('can:manageAccount,organisation');
    Route::get('manage/organisations/{organisation}/settings/systems', [OrganisationsController::class, 'systems'])->name('organisation.settings.systems')->middleware('can:managePlatforms,organisation');
    Route::get('manage/organisations/{organisation}/settings/systems/create', [OrganisationsController::class, 'createSystem'])->name('organisation.settings.system.create')->middleware('can:managePlatforms,organisation');
    Route::get('manage/organisations/{organisation}/invitations/{invitation}/resend', [OrganisationsController::class, 'resendInvitation'])->name('organisation.invitation.resend')->middleware(['can:manageMembers,organisation', 'throttle:3,10']);
    Route::post('organisations', [OrganisationsController::class, 'store'])->name('organisation.store');
    Route::post('organisations/{organisation}/exit', [OrganisationsController::class, 'exit'])->name('organisation.exit')->middleware('can:viewProfile,organisation');
    Route::post('organisations/{organisation}/invitations', [OrganisationsController::class, 'storeInvitation'])->name('organisation.invitation.store')->middleware('can:manageMembers,organisation');
    Route::post('manage/organisations/{organisation}/systems', [OrganisationsController::class, 'storeSystem'])->name('organisation.settings.system.store')->middleware('can:managePlatforms,organisation');
    Route::patch('organisations/{organisation}/members/{user}', [OrganisationsController::class, 'updateMember'])->name('organisation.members.update')->middleware(['can:manageMembers,organisation', 'can:manageMember,organisation,user']);
    Route::patch('organisations/{organisation}/members/{user}/transfer-owner', [OrganisationsController::class, 'transferOwner'])->name('organisation.members.transfer-owner')->middleware('can:viewMembers,organisation');
    Route::patch('organisations/{organisation}/settings/update-data', [OrganisationsController::class, 'updateData'])->name('organisation.settings.update_data')->middleware('can:manageAccount,organisation');
    Route::delete('organisations/{organisation}/delete', [OrganisationsController::class, 'delete'])->name('organisation.delete')->middleware('can:manageAccount,organisation');
    Route::delete('organisations/{organisation}/members/{user}', [OrganisationsController::class, 'deleteMember'])->name('organisation.members.delete')->middleware(['can:manageMembers,organisation', 'can:manageMember,organisation,user']);
    Route::delete('organisations/{organisation}/invitations/{invitation}', [OrganisationsController::class, 'deleteInvitation'])->name('organisation.invitation.delete')->middleware('can:manageMembers,organisation');
    Route::delete('organisations/{organisation}/systems/{system}', [OrganisationsController::class, 'deleteSystem'])->name('organisation.settings.system.delete')->middleware('can:managePlatforms,organisation');

    // Simulation
    Route::get('simulations/{simulation}', [SimulationsController::class, 'show'])->name('simulation.show')->middleware('can:view,simulation');
    Route::post('simulations/process-version/{processVersion}', [SimulationsController::class, 'store'])->name('simulation.store')->middleware('can:create,App\Models\Simulation,processVersion');
    Route::delete('simulations/{simulation}', [SimulationsController::class, 'delete'])->name('simulation.delete')->middleware('can:delete,simulation');

    // Demo
    Route::get('demos/{demo}', [DemosController::class, 'show'])->name('demo.show')->middleware('can:view,demo');
    Route::post('demos/solution-version/{solutionVersion}', [DemosController::class, 'store'])->name('demo.store')->middleware('can:create,App\Models\Demo,solutionVersion');
    Route::delete('demos/{demo}', [DemosController::class, 'delete'])->name('demo.delete')->middleware('can:delete,demo');

    // User
    Route::get('users/{user}', [UsersController::class, 'show'])->name('user.show');

    // Organisation
    Route::get('organisations/{organisation}', [OrganisationsController::class, 'show'])->name('organisation.show');

});

/**
 * Öffentliche Routen
 */
// Index und Home
Route::get('', [IndexController::class, 'index'])->name('index');

// Prozess Detailansicht - Information
Route::get('process/{namespace}/{identifier}', [ProcessesController::class, 'detail'])->name('process.detail');

// Prozess Detailansicht - Versions
Route::get('process/{namespace}/{identifier}/versions', [ProcessesController::class, 'detailVersions'])->name('process.detail.versions');

// Lösung Detailansicht - Information
Route::get('solution/{namespace}/{identifier}', [SolutionsController::class, 'detail'])->name('solution.detail');

// Lösung Detailansicht - Versions
Route::get('solution/{namespace}/{identifier}/versions', [SolutionsController::class, 'detailVersions'])->name('solution.detail.version');

// User-Login, Register, Passwort-Reset
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registrierung
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Passwort Reset
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// E-Mail Verifizierung
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend/{id}', [VerificationController::class, 'resend'])->name('verification.resend');

// Explore
Route::get('explore', [ExploreController::class, 'index'])->name('explore');
Route::get('explore/tag/{tag}', [ExploreController::class, 'tag'])->name('explore.tag');
Route::get('explore/search/{search}', [ExploreController::class, 'search'])->name('explore.search');

// Legal (AGBs und Datenschutzerklärung)
Route::get('legal/{section?}', [LegalController::class, 'index'])->name('legal');

// Invitation
Route::get('invitations/{invitation}/accept', [InvitationsController::class, 'accept'])->name('invitation.accept');

// Organisation
Route::get('organisations/{organisation}', [OrganisationsController::class, 'show'])->name('organisation.show');

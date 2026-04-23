<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserContext;
use App\Models\Organisation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Markdown;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\File;

/**
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller {

    /**
     * Show the home page
     * @return Application
     */
    public function index() {
        // Tags to show in the "ExploreProcesses" component.
        $tags = collect(explode(',', config('app.home.explore_processes.tags')))
            ->map('trim')
            ->filter(fn($item) => is_string($item) && $item)
            ->map(function ($item) {
                $pair = explode('|', $item);

                return [
                    'label' => $pair[0],
                    'value' => $pair[1]
                ];
            });

        $selectedTags = collect(explode(',', config('app.home.explore_processes.selected_tags')))
            ->map('trim')
            ->filter(fn($item) => is_string($item) && $item);

        return view('index', [
            'endpoint' => route('api.explore.index'),
            'tags' => $tags,
            'selectedTags' => $selectedTags,
            'countPerPage' => config('app.home.explore_processes.count_per_page'),
        ]);
    }

    /**
     * Changelog page.
     * @return View
     */
    public function changelog() {
        $content = '';
        $pathChangelog = base_path('changelog');
        $pathVersion = base_path('version');
        $version = File::exists($pathVersion) ? File::get($pathVersion) : '';

        if (File::exists($pathChangelog)) {
            $content = Markdown::parse(File::get($pathChangelog));
        }

        return view('changelog', ['content' => $content, 'version' => $version]);
    }

    /**
     * Switch the working context of the user. Switches between personal context (user profile)
     * and organisation context (organisation member)
     * @param UpdateUserContext $request
     * @return Application|RedirectResponse|Redirector
     */
    public function updateUserContext(UpdateUserContext $request) {
        $validated = $request->validated();
        $contextId = $validated['context'] ?? null;
        $user = auth()->user();
        $user->update(['context' => $contextId]);

        if (is_null($contextId)) {
            return redirect($user->profileProcessesPath());
        }

        $organisation = Organisation::findOrFail($contextId);

        return redirect(route('organisation.processes', ['organisation' => $organisation->namespace]));
    }
}

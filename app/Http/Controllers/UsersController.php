<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryList;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends Controller {

    /**
     * View a user profile.
     * @param User $user
     * @param QueryList $request
     * @return Application|Factory|View
     */
    public function show(User $user, QueryList $request) {
        $processesCollection = $user->publicProcesses();

        // Search
        $searchTerm = $request->query('search');

        if ($searchTerm) {
            $processesCollection = $processesCollection->filter(fn($process) => str_contains(strtolower($process->title), strtolower($searchTerm)));
        }

        // Sort
        $sort = $request->query('sort', 'lastChange_desc');
        [$sortField, $sortDirection] = explode('_', $sort);

        $processesCollection = match ($sortField) {
            'lastChange' => $sortDirection === 'asc' ? $processesCollection->sortBy('updated_at') : $processesCollection->sortByDesc('updated_at'),
            'alphabetically' => $sortDirection === 'asc' ? $processesCollection->sortBy('title') : $processesCollection->sortByDesc('title'),
            'complexity' => $processesCollection->sortBy(fn($process) => $process->latestPublishedVersion?->complexity_score, SORT_REGULAR, $sortDirection === 'desc'),
            default => $processesCollection->sortByDesc('updated_at'),
        };

        return view('users.show', [
            'user' => $user,
            'processes' => $processesCollection,
            'title' => $user->namespace
        ]);
    }
}

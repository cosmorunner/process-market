<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Models\Process;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 *
 */
class ExploreController extends Controller {

    /**
     * Explore-Startseite
     * @return Factory|View
     */
    public function index() {
        $processes = Process::whereVisibility(Visibility::Public->value)->limit(20)->inRandomOrder()->with('author')->get();

        return view('explore.index', ['processes' => $processes]);
    }

    /**
     * Nach einem Prozess-Tag suchen.
     * @param string $tag
     * @return Factory|View
     */
    public function tag(string $tag = '') {
        $processes = Tag::firstOrNew(['name' => $tag])->processes;

        return view('explore.index', ['processes' => $processes, 'tag' => $tag]);
    }

    /**
     * Suche nach Prozessen
     * @param string $search
     * @return Factory|View
     */
    public function search(string $search = '') {
        if ($search === '') {
            $processes = Process::whereVisibility(Visibility::Public->value)->limit(20)->get();

            return view('explore.index', ['processes' => $processes]);
        }

        $processes = DB::query()
            ->from('processes')
            ->select('processes.*', 'users.name as user_name', 'tags.name as tag_name')
            ->join('users', 'users.id', '=', 'processes.author_id')
            ->join('process_tag', 'processes.id', '=', 'process_tag.process_id')
            ->join('tags', 'tags.id', '=', 'process_tag.tag_id')
            ->where('processes.visibility', '=', Visibility::Public->value)
            ->where(function (Builder $query) use ($search) {
                $query->orWhere('processes.title', 'like', '%' . $search . '%');
                $query->orWhere('tags.name', 'like', '%' . $search . '%');
                $query->orWhere('users.name', 'like', '%' . $search . '%');
            })->limit(20)->get();

        $processes = Process::hydrate($processes->toArray());

        return view('explore.index', ['processes' => $processes]);
    }
}

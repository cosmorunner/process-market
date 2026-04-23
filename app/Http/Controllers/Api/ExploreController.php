<?php /** @noinspection PhpUnused */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExploreProcesses as ExploreProcessesRequest;
use App\Http\Resources\ExploreProcesses;
use App\Models\Process;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ExploreController
 * @package App\Http\Controllers\Api
 */
class ExploreController extends Controller {

    /**
     * @param ExploreProcessesRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ExploreProcessesRequest $request) {
        $validated = $request->validated();
        $itemsPerPage = $validated['items_per_page'] ?? 6;
        $search = $validated['search'] ?? null;
        $sort = $validated['sort'] ?? 'title';
        $tags = $validated['tags'] ?? [];

        return ExploreProcesses::collection(Process::public()->when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('title', 'ILIKE', '%' . $search . '%')
                    ->orWhere('description', 'ILIKE', '%' . $search . '%')
                    ->orWhereHas('tags', fn($query) => $query->where('name', 'ILIKE', '%' . $search . '%'));
            });
        })->when(count($tags) > 0, function ($query) use ($tags) {
            return $query->whereHas('tags', fn($query) => $query->whereIn('name', $tags));
        })->with('tags', 'author', 'latestPublishedVersion')->orderBy($sort)->paginate($itemsPerPage));

    }
}

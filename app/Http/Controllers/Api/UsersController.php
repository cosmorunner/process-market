<?php

namespace App\Http\Controllers\Api;

use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfilePicture;
use App\Http\Requests\UpdateUserSettings;
use App\Http\Resources\Process as ProcessResource;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UsersController extends Controller {

    /**
     * Suche nach Prozessen. Es wird in allen öffentlichen und privaten Prozessen gesucht auf denen
     * der Benutzer Zugriff hat.
     * @param string $search
     * @return JsonResponse
     */
    public function search($search = '') {
        if (strlen($search) < 1) {
            return response()->json();
        }

        /* @var User $user */
        $user = auth()->user();

        $builder = DB::query()
            ->from('processes')
            ->select('processes.*', 'tags.name as tag_name')
            ->join('users', 'users.id', '=', 'processes.author_id')
            ->leftJoin('process_tag', 'processes.id', '=', 'process_tag.process_id')
            ->leftJoin('tags', 'tags.id', '=', 'process_tag.tag_id')
            ->where(function (Builder $query) use ($user) {
                $query->orWhere('processes.visibility', '=', Visibility::Public->value);
                $query->orWhere('processes.author_id', '=', $user->id);
            })
            ->where(function (Builder $query) use ($search) {
                $query->orWhere('processes.title', 'like', '%' . $search . '%');
                $query->orWhere('processes.namespace', 'like', '%' . $search . '%');
                $query->orWhere('processes.identifier', 'like', '%' . $search . '%');
                $query->orWhere('tags.name', 'like', '%' . $search . '%');
            })
            ->limit(10);

        $processes = $builder->get();

        return response()->json(ProcessResource::collection(Process::hydrate($processes->toArray()))->toArray(request()));
    }

    /**
     * Gibt alle Graphen zurück die fertiggestellt sind und auf die der Benutzer Zugriff hat.
     * Diese werden z.B. für die Bearbeitung von Environments genutzt oder beim Erstellen von Menü-Punkten.
     * @param string|null $meta Optionale Angabe. Wenn vorhanden sollen nur die Basisdaten jedes Graphen zurückgegeben werden.
     * @return JsonResponse
     */
    public function processVersions(string $meta = null) {
        $processVersions = auth()->user()->accessiblePublishedProcessVersions();

        // Nur Basis-Metadaten
        if ($meta) {
            return response()->json($processVersions->map(fn(ProcessVersion $processVersion) => [
                [
                    'id' => $processVersion->id,
                    'title' => $processVersion->process->title,
                    'full_namespace' => $processVersion->latest_namespace
                ],
                [
                    'id' => $processVersion->id,
                    'title' => $processVersion->process->title,
                    'full_namespace' => $processVersion->full_namespace
                ],

            ])->flatten(1));
        }

        $graphSimpleArrays = collect();
        $grouped = $processVersions->groupBy('process_id');
        $grouped->each(function ($item) use ($graphSimpleArrays) {
            /* @var Collection $item */
            $processVersions = $item->sortBy('published_at');
            $processVersions->each(function (ProcessVersion $processVersion) use ($graphSimpleArrays) {
                $cache = $processVersion->cache();
                $graphSimpleArrays->push($cache['process_version_simple']);
            });
            /* @var ProcessVersion $latestVersion */
            $latestVersion = $processVersions->last();
            $cache = $latestVersion->cache();
            $graphSimpleArrays->push([
                ...$cache['process_version_simple'],
                'version' => 'latest',
                'full_namespace' => $latestVersion->latest_namespace
            ]);
        });

        return response()->json($graphSimpleArrays);
    }

    /**
     * Aktualisiert die Benutzereinstellungen
     * @return JsonResponse
     */
    public function updateSettings(UpdateUserSettings $request) {
        Setting::upsertSettings($request->validated(), Auth::user());

        return response()->json();
    }

    /**
     * Update profile picture.
     * @param UpdateProfilePicture $request
     * @return JsonResponse
     */
    public function profilePicture(UpdateProfilePicture $request) {
        /* @var UploadedFile $file */
        /* @var User $user */
        $user = auth()->user();
        $file = $request->validated()['file'];
        $image = Image::make($file);
        $name = Str::uuid()->toString();

        // Save temporarily locally
        $full = $image->resize(300, 300)->encode('jpg', 100);
        $fullName = $name . '.jpg';
        $full->save(Storage::disk('temp')->path($fullName));

        $thumb = $image->resize(30, 30)->encode('jpg', 100);
        $thumbName = $name . '_30x.jpg';
        $thumb->save(Storage::disk('temp')->path($thumbName));

        // Copy to default public disk
        Storage::disk(public_disc_name())->writeStream($fullName, Storage::disk('temp')->readStream($fullName));
        Storage::disk(public_disc_name())->writeStream($thumbName, Storage::disk('temp')->readStream($thumbName));

        // Delete temporary files
        Storage::disk('temp')->delete([$fullName, $thumbName]);

        // Delete previous images
        if (Uuid::isValid($user->image)) {
            Storage::disk(public_disc_name())->delete([
                $user->image . '.jpg',
                $user->image . '_30x.jpg',
            ]);
        }

        // Update in db.
        $user->update(['image' => $name]);

        return response()->json(['redirect' => route('profile.processes') . '?fm=' . base64_encode('Gespeichert')]);
    }

    /**
     * Toggle collapse/expand sidebar and save current state in user settings.
     * @return JsonResponse
     */
    public function toggleSidebar() {
        $collapse = (bool) Setting::retrieveUser('sidebar.collapse', false);
        Setting::upsertSettingForUser('sidebar.collapse', !$collapse);

        return response()->json();
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfilePicture;
use App\Models\Organisation;
use App\Models\ProcessVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;

/**
 * Class OrganisationsController
 * @package App\Http\Controllers\Api
 */
class OrganisationsController extends Controller {

    /**
     * Gibt alle Graphen zurück die fertiggestellt sind und auf die die Organisation Zugriff hat.
     * Diese werden z.B. für die Bearbeitung von Environments genutzt oder beim Erstellen von Menü-Punkten.
     * @param string|null $meta Optionale Angabe. Wenn vorhanden sollen nur die Basisdaten jeder Prozess-Version zurückgegeben werden.
     * @return JsonResponse
     */
    public function processVersions(Organisation $organisation, string $meta = null) {
        $processVersions = $organisation->accessiblePublishedProcessVersions();

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

        $graphSimpleArrays = $processVersions->map(function (ProcessVersion $processVersion) {
            $cache = $processVersion->cache();

            return [
                // Spezifische Version
                $cache['process_version_simple'],

                // Latest Version
                [
                    ...$cache['process_version_simple'],
                    'version' => 'latest',
                    'full_namespace' => $processVersion->latest_namespace
                ]
            ];
        })->flatten(1);

        return response()->json($graphSimpleArrays);
    }

    /**
     * Update profile picture.
     * @param UpdateProfilePicture $request
     * @param Organisation $organisation
     * @return JsonResponse
     */
    public function profilePicture(UpdateProfilePicture $request, Organisation $organisation) {
        /* @var UploadedFile $file */
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
        if (Uuid::isValid($organisation->image)) {
            Storage::disk(public_disc_name())->delete([
                $organisation->image . '.jpg',
                $organisation->image . '_30x.jpg',
            ]);
        }

        // Update in db.
        $organisation->update(['image' => $name]);

        return response()->json(['redirect' => route('organisation.processes', $organisation) . '?fm=' . base64_encode('Gespeichert')]);
    }

}

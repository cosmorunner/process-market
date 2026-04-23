<?php
/* @var App\Models\Process $process */
/* @var \App\Models\Role $role */
/* @var \Illuminate\Support\Collection $runningSimulationProcessIds */

?>
<div class="card">
    <div class="card-header p-2 d-flex justify-content-between border-bottom">
        <span class="text-truncate font-weight-bold">
            <a class="text-truncate" href="{{$process->devPath()}}">{{shorten((string) $process->title, 50)}}</a>
            <div>
                <small class="text-muted text-truncate">{{$process->full_namespace . '@' . $process->latest_version}}</small>
            </div>
        </span>
        <div class="d-flex justify-content-end">
            <a class="d-inline-block px-2" href="{{route('process.config', $process)}}">Konfiguration</a>
            <a class="d-inline-block px-2"
               href="{{route('process.demo', ['process' => $process, 'version' => 'develop']) . '?ref=' . base64_encode(route('organisation.processes', $organisation))}}">Demo</a>
            @if($archived)
                <a class="d-inline-block px-2" href="{{route('process.restore', $process )}}">Wiederherstellen</a>
            @else
                @if($role?->can('process_versions.create'))
                    <a class="d-inline-block px-2" href="{{route('process.complete', $process)}}">Fertigstellen</a>
                @endif
            @endif
            <div class="dropdown d-inline-block ml-2">
                <button class="btn btn-light py-0 btn-sm" type="button" id="dropdownMenuButton2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">more_vert</span>
                </button>
                <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropdownMenuButton2">
                    @if($role?->can('processes.update'))
                        <a class="dropdown-item" href="{{route('process.edit', $process)}}">Daten</a>
                    @endif
                    <a class="dropdown-item" href="{{route('process.versions', ['process' => $process, 'limit' => 10])}}">Versionen</a>
                    @if($role?->can('processes.create'))
                        <a class="dropdown-item"
                           href="{{route('process.create', ['template' => $process->full_namespace, 'namespace' => $organisation->namespace])}}">Als
                            Vorlage...</a>
                    @endif
                        @if($role?->can('processes.delete') && !$archived)
                        @if($process->hasPublishedVersion())
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{route('process.delete', $process)}}">Archivieren</a>
                        @else
                            <a class="dropdown-item text-danger" href="{{route('process.delete', $process)}}">Löschen</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($process->description || count($process->tags))
        <div class="card-body p-2">
            @if($process->description)
                <p class="card-text {{count($process->tags) ? 'mb-2' : ''}}">{{ \Illuminate\Support\Str::limit((string) $process->description, 250) }}</p>
            @endif
            @foreach ($process->tags as $tag)
                <small class="text-muted"><span class="material-icons" style="color:{{$tag->color}};">lens</span> {{$tag->name}}</small>
            @endforeach
        </div>
    @endif
    <div class="card-footer d-flex justify-content-between bg-white p-2">
        <div>
            @if($process->deleted_at !== null)
                <small class="text-muted">
                    <span class="material-icons">inventory</span> Archiviert
                </small>
            @endif
            @if($process->visibility === App\Enums\Visibility::Private->value)
                <small class="text-muted">
                    <span class="material-icons">lock</span> Privat
                </small>
            @endif
            @if($process->visibility === App\Enums\Visibility::Hidden->value)
                <small class="text-muted">
                    <span class="material-icons">visibility_off</span> Versteckt
                </small>
            @endif
            @if($process->visibility === App\Enums\Visibility::Public->value)
                <small class="text-muted">
                    <span class="material-icons">language</span> Öffentlich
                </small>
            @endif
        </div>
        <div>
            @include('partials.complexity-score', ['score' => $process->latestVersion->complexity_score])
        </div>
    </div>
</div>

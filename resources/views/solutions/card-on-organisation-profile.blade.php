<?php
/* @var App\Models\Solution $solution */
/* @var \App\Models\Role $role */

?>
<div class="card">
    <div class="card-header p-2 d-flex justify-content-between border-bottom">
        <span class="text-truncate font-weight-bold">
            <a class="text-truncate" href="{{$solution->configPath()}}">{{shorten((string) $solution->name, 50)}}</a>
            <div>
                <small class="text-muted text-truncate">{{$solution->full_namespace . '@' . $solution->latest_version}}</small>
            </div>
        </span>
        <div class="d-flex justify-content-end">
            <a class="d-inline-block px-2"
               href="{{route('solution.demo', ['solution' => $solution, 'version' => 'develop']) . '?ref=' . base64_encode(route('organisation.solutions', $organisation))}}">Benutzer-Demo</a>
            @if($role?->can('solution_version.create'))
                <a class="d-inline-block px-2" href="{{route('solution.complete', $solution)}}">Fertigstellen</a>
            @endif
            <div class="dropdown d-inline-block ml-2">
                <button class="btn btn-light py-0 btn-sm" type="button" id="dropdownMenuButton2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="material-icons">more_vert</span>
                </button>
                <div class="dropdown-menu scrollable-dropdown" aria-labelledby="dropdownMenuButton2">
                    @if($role?->can('solutions.update'))
                        <a class="dropdown-item" href="{{route('solution.edit', $solution)}}">Daten</a>
                    @endif
                    <a class="dropdown-item" href="{{route('solution.versions', $solution)}}">Versionen</a>
                    @if($role?->can('solutions.delete'))
                        <div class="dropdown-divider"></div>
                        @if($solution->hasPublishedVersion())
                            <a class="dropdown-item text-danger" href="{{route('solution.delete', $solution)}}">Archivieren</a>
                        @else
                            <a class="dropdown-item text-danger" href="{{route('solution.delete', $solution)}}">Löschen</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($solution->description || count($solution->tags))
        <div class="card-body p-2">
            @if($solution->description)
                <p class="card-text {{count($solution->tags) ? 'mb-2' : ''}}">{{ \Illuminate\Support\Str::limit((string) $solution->description, 250) }}</p>
            @endif
            @foreach ($solution->tags as $tag)
                <small class="text-muted"><span class="material-icons" style="color:{{$tag->color}};">lens</span> {{$tag->name}}</small>
            @endforeach
        </div>
    @endif
    <div class="card-footer d-flex justify-content-between bg-white p-2">
        <div>
            @if($solution->deleted_at !== null)
                <small class="text-muted">
                    <span class="material-icons">inventory</span> Archiviert
                </small>
            @endif
            @if($solution->deleted_at === null && $solution->visibility === App\Enums\Visibility::Private->value)
                <small class="text-muted">
                    <span class="material-icons">lock</span> Privat
                </small>
            @endif
            @if($solution->deleted_at === null && $solution->visibility === App\Enums\Visibility::Hidden->value)
                <small class="text-muted">
                    <span class="material-icons">visibility_off</span> Versteckt
                </small>
            @endif
            @if($solution->deleted_at === null && $solution->visibility === App\Enums\Visibility::Public->value)
                <small class="text-muted">
                    <span class="material-icons">language</span> Öffentlich
                </small>
            @endif
        </div>
        <div>
            @include('partials.complexity-score', ['score' => $solution->latestVersion->complexity_score])
        </div>
    </div>
</div>

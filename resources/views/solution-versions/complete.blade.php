<?php

use App\Enums\Visibility;
use Illuminate\Support\Collection;

/* @var App\Models\Solution $solution */
/* @var App\Models\SolutionVersion $solutionVersion */
/* @var App\Models\ProcessVersion[]|Collection $graphs */
/* @var App\Models\ProcessVersion $processVersion */

?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container pt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ url()->previous() }}">Zurück</a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between">
                <div>
                    <h3>{{$solution->name}}</h3>
                    <span class="text-muted">{{$solution->full_namespace}}</span>
                </div>
                <div>
                    <a href="{{route('solution.demo', ['solution' => $solution, 'version' => $solution->latest_version]) . '?ref=' . urlencode(url()->current())}}"
                       class="btn btn-sm btn-outline-primary">
                        <span class="material-icons">desktop_mac</span> Demo
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div>
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active bg-white border-bottom-white" href="#">Fertigstellen</a>
                        </li>
                    </ul>
                </div>
                <div class="container bg-white border border-top-0 p-3">
                    <div class="row">
                        <div class="col">
                            <div class="alert alert-info" role="alert">
                                <ul class="mb-0 pl-2">
                                    <li>Fertiggestellte Versionen markieren einen <b>Meilenstein in der
                                            Entwicklung</b> der Lösung und können <b>nachträglich nicht mehr
                                            verändert</b> werden.
                                    </li>
                                    <li>Nur <b>fertiggestellte Versionen</b> können bei entsprechender
                                        Lösung-Sichtbarkeit ("Öffentlich" oder "Versteckt"), durch Benutzer <b>gefunden
                                            werden</b>.
                                    </li>
                                    <li>Nach dem Fertigstellen einer Version, wird <b>automatisch eine neue "In der
                                            Entwicklung"-Version</b> angelegt.
                                    </li>
                                    <li>Die <b>Admin-Demo</b> der Version kann nicht mehr verändert werden.</li>
                                </ul>
                            </div>

                            @if($processVersions->isEmpty())
                                <div class="alert alert-warning" role="alert">
                                    <span>Diese Version hat keine Prozesse.</span>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col">
                                        <span class="d-block">Prozesse</span>
                                        @if($hasPrivateProcess)
                                            <div class="alert alert-warning" role="alert">
                                                Diese Version hat private Prozesse. Im Falle einer Veröffentlichung
                                                (Sichtbarkeit: Versteckt oder öffentlich) wird diese Version nicht
                                                angezeigt.
                                            </div>
                                        @endif
                                        <ul class="list-group list-group-flush mt-2">
                                            @foreach($processVersions->sortBy('full_namespace') as $processVersion)
                                                <li class="list-group-item p-2">
                                                    <span>{{ $processVersion->full_namespace }}</span>
                                                    <span> - </span>
                                                    <span class="text-muted">
                                                        @if($processVersion->process->visibility == Visibility::Public->value)
                                                            <span class="material-icons">language</span>
                                                            <span>Öffentlich</span>
                                                        @endif
                                                        @if($processVersion->process->visibility == Visibility::Hidden->value)
                                                            <span class="material-icons">visibility_off</span>
                                                            <span>Versteckt</span>
                                                        @endif
                                                        @if($processVersion->process->visibility == Visibility::Private->value)
                                                            <span class="material-icons">lock</span>
                                                            <span class="text-warning">Privat</span>
                                                        @endif
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <form method="POST"
                                  action="{{ route('solution_version.publish', ['solutionVersion' => $solutionVersion]) }}">
                                @csrf
                                @method('PATCH')
                                <div class="form-group mt-4">
                                    <label for="version">Version</label>
                                    <input type="text" value="{{old('version') ?? $nextVersion}}"
                                           class="form-control {{$errors->{ $bag ?? 'default'}->has('version') ? 'is-invalid' : '' }}"
                                           name="version" aria-describedby="titleHelp" placeholder="">
                                    <small class="text-muted">Tragen Sie mindestens die Version "{{$nextVersion}}" ein.</small>
                                    @foreach ($errors->{ $bag ?? 'default' }->get('version') as $error)
                                        <div class="invalid-feedback d-block">{{$error}}</div>
                                    @endforeach
                                </div>
                                @if($solutionVersion->solution->hasPublishedVersion())
                                    <div class="form-group">
                                        <label for="changelog">Changelog</label>
                                        <textarea
                                                class="form-control {{$errors->{ $bag ?? 'default'}->has('changelog') ? 'is-invalid' : '' }}"
                                                name="changelog" rows="4">{{old('changelog')}}</textarea>
                                        <small class="text-muted">Beschreiben Sie die Änderungen im Vergleich zur vorherigen Version.</small>
                                        @foreach ($errors->{ $bag ?? 'default' }->get('changelog') as $error)
                                            <div class="invalid-feedback d-block">{{$error}}</div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col">
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-sm btn-success">Version
                                                fertigstellen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

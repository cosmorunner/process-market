<?php
/* @var App\Models\Process $process */
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
                    <h3>{{$process->title}}</h3>
                    <span class="text-muted">{{$process->full_namespace}}</span>
                </div>
                <div>
                    <a href="{{route('process.config', ['process' => $process, 'version' => $process->latest_version])}}"
                       class="btn btn-sm btn-outline-primary">
                        Konfiguration
                    </a>
                    <a href="{{route('process.develop', ['process' => $process, 'version' => $process->latest_version])}}"
                       class="btn btn-sm btn-outline-primary">
                        Regeln & Daten
                    </a>
                    <a href="{{route('process.demo', ['process' => $process, 'version' => $process->latest_version]) . '?ref=' . urlencode(url()->current())}}"
                       class="btn btn-sm btn-outline-primary">
                        Demo
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
                                            Entwicklung</b> des Prozesses und können <b>nachträglich nicht mehr
                                            verändert</b> werden.
                                    </li>
                                    <li>Nur <b>fertiggestellte Versionen</b> können bei entsprechender
                                        Prozess-Sichtbarkeit ("Öffentlich" oder "Versteckt"), durch Benutzer <b>gefunden
                                            werden</b>.
                                    </li>
                                    <li>Nach dem Fertigstellen einer Version, wird <b>automatisch eine neue "In der
                                            Entwicklung"-Version</b> angelegt.
                                    </li>
                                </ul>
                            </div>

                            <form method="POST" action="{{ route('process_version.publish', ['processVersion' => $processVersion]) }}">
                                @csrf
                                @method('PATCH')
                                <div class="form-group">
                                    <label for="version">Version</label>
                                    <input type="text"
                                           value="{{old('version') ?? $nextVersion}}"
                                           class="form-control {{$errors->{ $bag ?? 'default'}->has('version') ? 'is-invalid' : '' }}"
                                           name="version"
                                           aria-describedby="titleHelp"
                                           placeholder="">
                                    <small class="text-muted">Tragen Sie mindestens die Version "{{$nextVersion}}" ein.</small>
                                    @foreach ($errors->{ $bag ?? 'default' }->get('version') as $error)
                                        <div class="invalid-feedback d-block">{{$error}}</div>
                                    @endforeach
                                </div>
                                <div class="mb-3">
                                    @include('partials.process-dependencies', ['dependencies' => $processVersion->definition->dependencies])
                                </div>
                                @if($processVersion->process->hasPublishedVersion())
                                    <div class="form-group">
                                        <label for="changelog">Changelog</label>
                                        <textarea
                                            class="form-control {{$errors->{ $bag ?? 'default'}->has('changelog') ? 'is-invalid' : '' }}"
                                            name="changelog" rows="4">{{old('changelog')}}</textarea>
                                        <small class="text-muted">Beschreiben Sie die Änderungen im Vergleich zur
                                            vorherigen Version.</small>
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

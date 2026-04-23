<?php
/* @var App\Models\Solution $solution */
/* @var App\Models\Demo|null $mainDemo */

?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ $solution->authorProfileSolutionPath() }}">Zur Übersicht</a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between">
                <div>
                    <h3>{{$solution->name}}</h3>
                    <span class="text-muted">{{$solution->latestVersion->full_namespace}}</span>
                </div>
                <div>
                    @if(isset($mainDemo) && $mainDemo instanceof \App\Models\Demo && $canUpdateSolution)
                        <a href="{{route('demo.show', ['demo' => $mainDemo]) . '?ref=' . base64_encode(url()->current())}}"
                           class="btn btn-sm btn-outline-primary">
                            Admin-Demo Konfiguration
                        </a>
                    @endif
                    <a href="{{route('solution.demo', ['solution' => $solution, 'version' => $solution->latest_version, 'organisation' => $solution->author instanceof \App\Models\Organisation ? $solution->author : null]) . '?ref=' . base64_encode(url()->current())}}"
                       class="btn btn-sm btn-outline-primary">
                        Neue Benutzer-Demo
                    </a>
                </div>
            </div>
        </div>
        @if(!$solution->hasPublishedVersion() && $solution->latestVersion && $canCompleteVersion)
            <div class="row">
                <div class="col">
                    <div class="alert alert-light d-flex justify-content-between" role="alert">
                        <span>Diese Lösung besitzt noch keine fertiggestellte Version.</span>
                        <a href="{{route('solution.complete',['solution' => $solution])}}">
                            <button class="btn btn-sm btn-success">Erste Version fertigstellen</button>
                        </a>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col">
                <div>
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'solutions.config' ? 'active bg-white border-bottom-white' : ''}}"
                               href="{{route('solution.config', ['solution' => $solution])}}">Konfiguration</a>
                        </li>
                        @if($canUpdateSolution)
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'solutions.meta-data' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('solution.edit', ['solution' => $solution])}}">Daten</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'solutions.versions' ? 'active bg-white border-bottom-white' : ''}}"
                               href="{{route('solution.versions', ['solution' => $solution])}}">Versionen</a>
                        </li>
                        @if($canDeleteSolution)
                            <li class="nav-item">
                                <a class="nav-link text-danger {{$viewName === 'solutions.delete' || $viewName === 'solutions.archive' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('solution.delete', ['solution' => $solution])}}">{{ $solution->hasPublishedVersion() ? 'Archivieren' : 'Löschen'  }}</a>
                            </li>
                        @endif
                    </ul>
                </div>

                @yield('solutions.edit.content')
            </div>
        </div>
    </div>
@endsection

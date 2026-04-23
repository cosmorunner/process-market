<?php
/* @var App\Models\Process $process */

?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ $process->authorProfileProcessesPath() }}">Zur Übersicht</a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between">
                <div>
                    <h3>
                        <span>{{$process->title}}</span>
                        @if($process->isArchived())
                            <span class="badge badge-dark"><span
                                        class="material-icons">block</span> <span>Archiviert</span></span>
                        @endif
                    </h3>
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
                    <a href="{{route('process.demo', ['process' => $process, 'version' => $process->latest_version, 'organisation' => $process->author instanceof \App\Models\Organisation ? $process->author : null]) . '?ref=' . base64_encode(url()->current())}}"
                       class="btn btn-sm btn-outline-primary">
                        Demo
                    </a>
                </div>
            </div>
        </div>
        @if(!$process->hasPublishedVersion() && $process->latestVersion)
            <div class="row">
                <div class="col">
                    <div class="alert alert-light d-flex justify-content-between" role="alert">
                        <span>Dieser Prozess besitzt noch keine fertiggestellte Version.</span>
                        <a href="{{route('process.complete',['process' => $process])}}">
                            <button class="btn btn-sm btn-success">Erste Version fertigstellen</button>
                        </a>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col">
                <div>
                    <ul class="nav nav-tabs d-flex justify-content-between">
                        <div class="d-flex flex-row">
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'processes.meta-data' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('process.edit', ['process' => $process])}}">Daten</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'processes.versions' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('process.versions', ['process' => $process, 'limit' => 10])}}">Versionen</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger {{($viewName === 'processes.delete' || $viewName === 'processes.archive' || $viewName === 'processes.restore') ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route( $process->isArchived() ? 'process.restore' : 'process.delete', ['process' => $process])}}">{{ $process->isArchived() ? 'Wiederherstellen' : ($process->hasPublishedVersion() ? 'Archivieren' : 'Löschen')  }}</a>
                            </li>
                        </div>
                        @if($viewName === 'processes.versions')
                            <div class="pr-1" style="height: 40px;">
                                @include('partials.docs', ['article' => 'export'])
                            </div>
                        @endif
                    </ul>
                </div>
                @yield('processes.edit.content')
            </div>
        </div>
    </div>
@endsection

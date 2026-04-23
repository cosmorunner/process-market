<?php

use App\Models\Process;
use App\Models\User;
use App\Models\ProcessVersion;

/* @var ProcessVersion $processVersion */
/* @var Process $process */
?>

@extends('processes.edit')
@section('processes.edit.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                <table class="table table-sm m-0">
                    <tbody>
                    @foreach($process->versions as $processVersion)
                        <tr class="d-flex">
                            <td class="p-2 col-5 @if ($loop->first) border-0 @endif">
                                <a class="mb-2 d-inline-block"
                                   href="{{route('process.develop', ['process' => $process, 'version' => $processVersion->version])}}">{{$processVersion->version}}</a>
                                @if(!$processVersion->isPublished())
                                    <span class="badge badge-primary">In der Entwicklung</span>
                                @endif
                                @if($processVersion->created_at == $process->updated_at)
                                    <span class="badge badge-light">Identisch zur Vorgängerversion</span>
                                @endif
                                @if($processVersion->id == $process->latest_published_version_id)
                                    <span class="badge badge-primary">latest</span>
                                @endif
                                @foreach($processVersion->synchronizations->where('response_code', '=', \Illuminate\Http\Response::HTTP_OK)->sortByDesc('created_at')->unique('system_id') as $synchronisation)
                                    <a href="{{$synchronisation->system->url}}" target="_blank">
                                    <span class="badge badge-light mr-1" data-toggle="tooltip" data-placement="top"
                                          title="Synchronisiert am {{ $synchronisation->created_at->format('d.m.Y') }}">
                                        <span class="material-icons text-success">done</span>
                                        <span>{{$synchronisation->system->name}}</span>
                                    </span>
                                    </a>
                                @endforeach
                                <div class="py-2">
                                    <span class="mb-0 text-muted"
                                          style="white-space: pre-line">{{$processVersion->changelog}}</span>
                                </div>
                                @include('partials.process-dependencies', ['dependencies' => $processVersion->dependencies])
                            </td>
                            <td class="p-2 col-2 @if ($loop->first) border-0 @endif">
                                @if($processVersion->isPublished())
                                    @include('partials.complexity-score', ['score' => $processVersion->complexity_score])
                                @endif
                            </td>
                            <td class="p-2 d-flex justify-content-end col-5 @if ($loop->first) border-0 @endif">
                                <!-- An archived not published version shall not be published -->
                                @if($processVersion->isPublished() || !$process->isArchived())
                                    @if(!$processVersion->isPublished())
                                        <a href="{{route('process.complete', ['process' => $process])}}">
                                            <button class="btn btn-success px-2 py-0">Fertigstellen</button>
                                        </a>
                                    @else
                                        <div class="d-flex flex-column justify-content-between">
                                            <div class="d-flex justify-content-end">
                                                <a class="mr-2"
                                                   href="{{route('process.demo', ['process' => $process, 'version' => $processVersion->version]) . '?ref=' . base64_encode(url()->current())}}">
                                                    <button class="btn btn-outline-primary px-2 py-0">Demo</button>
                                                </a>
                                                <a class="mr-2"
                                                   href="{{route('process_version.sync', ['processVersion' => $processVersion])}}">
                                                    <button class="btn btn-primary px-2 py-0">Exportieren</button>
                                                </a>
                                                @if(!$process->isArchived())
                                                    <div class="dropdown d-inline-block show">
                                                        <button type="button" id="moreOptions" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="true"
                                                                class="btn btn-sm btn-light">
                                                            <span class="material-icons">more_vert</span>
                                                        </button>
                                                        <div aria-labelledby="moreOptions" class="dropdown-menu">
                                                            <a class="dropdown-item"
                                                               href="{{route('process_version.restore', $processVersion)}}">
                                                                Wiederherstellen
                                                            </a>
                                                            @if(auth()->user()->can('download', $processVersion))
                                                                <a class="dropdown-item"
                                                                   href="{{route('process_version.download', $processVersion)}}">
                                                                    Herunterladen
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted text-right">{{ $processVersion->updated_at->diffForHumans() }}</small>
                                            @if($processVersion->publisher instanceof User)
                                                <small class="text-muted text-right">Version fertiggestellt
                                                    durch {{ $processVersion->publisher?->namespace }}</small>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- We only load based on limit query param. "versions_count" is the true number of versions. -->
        @if($process->versions->count() < $process->versions_count)
            <div class="row">
                <div class="col d-flex justify-content-center">
                    <a href="{{route('process.versions', $process)}}">Alle Versionen anzeigen</a>
                </div>
            </div>
        @endif
    </div>
@endsection

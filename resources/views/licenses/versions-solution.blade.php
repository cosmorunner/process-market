<?php

use App\Models\Process;

/* @var App\Models\Solution $solution */
/* @var App\Models\License $license */
/* @var App\Models\Synchronization $synchronisation */
/* @var App\Models\SolutionVersion $latestPublicVersion */
?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ $license->ownerProfileLicensesPath(['f' => 'solutions']) }}">Zurück zu Lizenzen</a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between">
                <div>
                    <h3>{{$solution->name}}</h3>
                    <span class="text-muted">{{$solution->full_namespace}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            @if($missingVersions)
                <div class="col-12">
                    <div class="alert alert-info d-flex justify-content-between" role="alert">
                        <div>
                            <span>Manche Versionen stehen aufgrund von fehlenden Prozess-Lizenzen nicht zur Verfügung.</span>
                            @if($latestPublicVersion)
                                <span>Aktuellste Version: {{ $latestPublicVersion->version }}</span>
                            @endif
                        </div>
                        @if($solution->isPubliclyAccessible())
                            <a class="btn btn-sm btn-success"
                               href="{{route('solution.purchase', ['solution' => $solution, 'version' => 'latest', 'namespace' => $license->owner->namespace])}}">
                                <span class="material-icons">balance</span>
                                <span>Lizenz aktualisieren</span>
                            </a>
                            <a class="btn btn-sm btn-primary"
                               href="{{route('solution.detail.version', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier])}}">
                                <span class="material-icons">list</span>
                                <span>Öffentliche Versionen</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col">
                <div class="container bg-white border border-bottom-0">
                    <div class="row">
                        <div class="col px-4 py-3">
                            <span class="material-icons text-secondary">balance</span>
                            <span>{{$license->typeTitle()}} Lizenz - Erworben am {{$license->created_at->format('d.m.Y')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="container bg-white border p-3">
                    <div class="row">
                        <div class="col">
                            <table class="table table-sm m-0">
                                <tbody>
                                @foreach($solution->versions->sortByDesc('created_at') as $solutionVersion)
                                    <tr class="d-flex">
                                        <td class="p-2 col-6 @if ($loop->first) border-0 @endif">
                                            <span>{{$solutionVersion->version}}</span>
                                            @if($solutionVersion->id == $solution->latest_published_version_id)
                                                <span class="badge badge-primary">latest</span>
                                            @endif
                                            @foreach($solutionVersion->synchronizationsOfSystems($systems)->where('response_code', '=', \Illuminate\Http\Response::HTTP_OK)->sortByDesc('created_at')->unique('system_id') as $synchronisation)
                                                <a href="{{$synchronisation->system->url}}" target="_blank">
                                                    <span class="badge badge-light mr-1" data-toggle="tooltip"
                                                          data-placement="top"
                                                          title="Synchronisiert am {{ $synchronisation->created_at->format('d.m.Y') }}">
                                                        <span class="material-icons text-success">done</span>
                                                        <span>{{$synchronisation->system->name}}</span>
                                                    </span>
                                                </a>
                                            @endforeach
                                            <div>
                                                <span class="mb-0 text-muted"
                                                      style="white-space: pre-line">{{$solutionVersion->changelog}}</span>
                                            </div>
                                            <div class="mt-2">
                                                @foreach(collect($solutionVersion->processTypes())->sort() as $fullNamespace)
                                                    @php
                                                        /* @var Process $process */
                                                        $process = $solutionVersion->processes()->first(fn(Process $process) => $process->full_namespace === explode('@', $fullNamespace)[0])
                                                    @endphp
                                                    <span class="d-block mb-1">
                                                        <img src="{{$process->author->thumbnailPath()}}"
                                                             class="rounded-circle d-inline-block mr-1" height="25"
                                                             alt=""/>
                                                        <span>{{$process->title}} - {{$fullNamespace}}</span>
                                                        @if(!$process->isPubliclyAccessible())
                                                            <span class="material-icons text-muted"
                                                                  title="Privater Prozess">lock</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="p-2 col-2 @if($loop->first) border-0 @endif">
                                            @include('partials.complexity-score', ['score' => $solutionVersion->complexity_score])
                                        </td>
                                        <td class="p-2 col-4 d-flex justify-content-end @if ($loop->first) border-0 @endif">
                                            <div class="d-flex flex-column justify-content-between">
                                                <a href="{{route('license.demo', ['license' => $license, 'version' => $solutionVersion->version]) . '?ref=' . base64_encode(url()->current())}}"
                                                   class="d-flex justify-content-end">
                                                    <button class="btn btn-outline-primary px-2 py-0">Benutzer-Demo
                                                    </button>
                                                </a>
                                                <small class="text-muted text-right">{{ $solutionVersion->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

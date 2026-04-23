<?php

use App\Enums\Visibility;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Solution;
use App\Models\SolutionVersion;

/* @var Solution $solution */
/* @var Process $process */
/* @var SolutionVersion $solutionVersion */
/* @var SolutionVersion $latestPublicSolutionVersion */

?>

@extends('solutions.edit')

@section('solutions.edit.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                @if($solution->visibility > Visibility::Hidden->value && $latestPublicSolutionVersion && $latestPublicSolutionVersion->version !== $solution->latestPublishedVersion->version)
                    <div class="alert alert-info" role="alert">
                        Die aktuellste, öffentliche Version ist <b>"{{ $latestPublicSolutionVersion->version }}"</b>
                        aufgrund von privaten
                        Prozessen.
                    </div>
                @endif
                <table class="table table-sm m-0">
                    <tbody>
                    @foreach($solution->versions as $solutionVersion)
                        <tr class="d-flex">
                            <td class="p-2 col-6 @if ($loop->first) border-0 @endif">
                                @if(!$solutionVersion->isPublished())
                                    <a class="d-inline-block"
                                       href="{{route('solution.config', $solution)}}">{{$solutionVersion->version}}</a>
                                    <span class="badge badge-primary">In der Entwicklung</span>
                                @else
                                    <span class="d-inline-block">{{$solutionVersion->version}}</span>
                                    @if($solutionVersion->id == $solution->latest_published_version_id)
                                        <span class="badge badge-primary">latest</span>
                                    @endif
                                    @foreach($solutionVersion->synchronizations->where('response_code', '=', \Illuminate\Http\Response::HTTP_OK)->sortByDesc('created_at')->unique('system_id') as $synchronisation)
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
                                @endif
                                <div class="mt-2">
                                    @foreach(collect($solutionVersion->processTypes())->sort() as $fullNamespace)
                                        @php
                                            /* @var Process $process */
                                            $process = $solutionVersion->processes()->first(fn(Process $process) => $process->full_namespace === explode('@', $fullNamespace)[0])
                                        @endphp
                                        <span class="d-block mb-1">
                                            <img src="{{$process->author->thumbnailPath()}}"
                                                 class="d-inline-block rounded-circle mr-1" height="25" alt=""/>
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
                                @if($solutionVersion->isPublished())
                                    @include('partials.complexity-score', ['score' => $solutionVersion->complexity_score])
                                @endif
                            </td>
                            <td class="p-2 col-4 d-flex justify-content-end @if ($loop->first) border-0 @endif">
                                @if(!$solutionVersion->isPublished())
                                    <a href="{{route('solution.demo', ['solution' => $solution, 'version' => $solutionVersion->version, 'organisation' => $solution->author instanceof Organisation ? $solution->author : null]) . '?ref=' . base64_encode(url()->current())}}">
                                        <button class="btn btn-outline-primary px-2 py-0">
                                            <span>Benutzer-Demo</span>
                                        </button>
                                    </a>
                                    @if($canCompleteVersion)
                                        <a class="ml-2"
                                           href="{{route('solution.complete', ['solution' => $solution])}}">
                                            <button class="btn btn-success px-2 py-0">Fertigstellen</button>
                                        </a>
                                    @endif
                                @else
                                    <div class="d-flex flex-column justify-content-between">
                                        <a href="{{route('solution.demo', ['solution' => $solution, 'version' => $solutionVersion->version, 'organisation' => $solution->author instanceof Organisation ? $solution->author : null]) . '?ref=' . base64_encode(url()->current())}}">
                                            <button class="btn btn-outline-primary px-2 py-0">Benutzer-Demo</button>
                                        </a>
                                        <small class="text-muted text-right">{{ $solutionVersion->updated_at->diffForHumans() }}</small>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('partials.docs', ['article' => 'export'])
@endsection

<?php
/**
 * @var App\Models\Solution $solution
 * @var App\Models\ProcessVersion $processVersion
 * @var App\Models\SolutionVersion $version
 */
?>

@extends('solutions.detail')

@section('solutions.detail.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                @if($solutionVersion)
                    <table class="table table-sm m-0">
                        <tbody>
                        @foreach($versions as $version)
                            <tr class="d-flex">
                                <td class="p-2 col-6 @if ($loop->first) border-0 @endif">
                                    <span class="mb-2 d-inline-block">{{$version->version}}</span>
                                    <div>
                                        <span class="mb-0 text-muted" style="white-space: pre-line">{{$version->changelog}}</span>
                                    </div>
                                    <div class="mt-2">
                                        @foreach(collect($version->processTypes())->sort() as $fullNamespace)
                                            @php
                                                /* @var \App\Models\Process $process */
                                                $process = $version->processes()->first(fn(\App\Models\Process $process) => $process->full_namespace === explode('@', $fullNamespace)[0])
                                            @endphp
                                            <span class="d-block mb-1">
                                            <a href="{{$process->author->publicPath()}}">
                                                <img src="{{$process->author->thumbnailPath()}}" class="d-inline-block mr-1" height="25" alt=""/>
                                            </a>
                                            <a href="{{route('process.detail', ['namespace' => $process->namespace, 'identifier' => $process->identifier])}}">
                                                <span>{{$process->title}} - {{$fullNamespace}}</span>
                                            </a>
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-2 col-2 @if($loop->first) border-0 @endif">
                                    @include('partials.complexity-score', ['score' => $version->complexity_score])
                                </td>
                                <td class="p-2 col-4 d-flex justify-content-end @if ($loop->first) border-0 @endif">
                                    <div class="d-flex flex-column justify-content-between">
                                        <div class="d-flex">
                                            <a class="btn btn-sm btn-outline-success mr-2"
                                               href="{{ route('solution.purchase', ['solution' => $solution, 'version' => $version->version]) }}">
                                                <span class="material-icons">balance</span>
                                                <span>Lizenzen</span>
                                            </a>
                                            <a class="btn btn-primary px-2 py-0"
                                               href="{{route('solution.detail.demo', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier, 'version' => $version->version]) . '?ref=' . base64_encode(url()->current())}}">
                                                <span>Demo</span>
                                            </a>
                                        </div>
                                        <small class="text-muted text-right">{{ $version->updated_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <span class="text-muted">
                        <i>Keine öffentliche Version verfügbar.</i>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endsection

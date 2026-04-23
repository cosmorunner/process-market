<?php
/**
 * @var App\Models\Solution $solution
 * @var App\Models\ProcessVersion $processVersion
 * @var App\Models\SolutionVersion $solutionVersion
 */
?>

@extends('solutions.detail')

@section('solutions.detail.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                @if($solution->description)
                    <p class="text-muted">{{$solution->description}}</p>
                @else
                    <span class="text-muted">
                        <i>Keine Beschreibung</i>
                    </span>
                @endif
                <hr/>
                @if($solutionVersion)
                    <span class="d-block py-2">Prozesse</span>
                    <ul class="list-group list-group-flush border-top-0">
                        @foreach($solutionVersion->processVersions() as $processVersion)
                            <li class="list-group-item py-2 px-0">
                                <a href="{{$processVersion->process->author->publicPath()}}">
                                    <img src="{{$processVersion->process->author->thumbnailPath()}}" class="d-inline-block rounded-circle mr-1" height="25" alt=""/>
                                </a>
                                <a href="{{route('process.detail', ['namespace' => $processVersion->process->namespace, 'identifier' => $processVersion->process->identifier])}}">{{$processVersion->process->title}}
                                    - {{$processVersion->full_namespace}}</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted">
                        <i>Keine öffentliche Version verfügbar.</i>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endsection

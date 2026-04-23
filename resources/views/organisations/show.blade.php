<?php

/* @var \App\Models\Organisation $organisation */

?>

@extends ('layouts.app')

@section('content')
    <div class="container">
        <div class="row pt-4">
            <div class="col-lg-3 col-md-4 col-12 float-md-left pr-md-3 pr-xl-6">
                <div class="clearfix mb-4">
                    <div class="col-12 col-md-12 bg-white border rounded">
                        <div class="p-3">
                            <div class="d-flex justify-content-center">
                                <img src="{{$organisation->imagePath()}}" class="rounded-circle" alt="" width="130"
                                     height="130"/>
                            </div>
                            <h3 class="text-center mt-3">{{$organisation->name}}</h3>
                            <h5 class="text-muted text-center">{{$organisation->namespace}}</h5>
                            @if($organisation->description)
                                <hr/>
                                <p class="text-muted text-center">{{$organisation->description}}</p>
                            @endif
                            @if($organisation->city)
                                <span class="text-muted d-flex justify-content-center">
                                    <span>
                                        <span class="material-icons">place</span>
                                        <span>{{$organisation->city}}</span>
                                    </span>
                                </span>
                            @endif
                            @if($organisation->link)
                                <span class="text-muted d-flex justify-content-center">
                                    <span>
                                        <span class="material-icons">open_in_new</span>
                                        <a class="text-muted" href="{{$organisation->link}}" target="_blank">Website</a>
                                    </span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-12 float-md-left pl-md-2">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link {{$viewName === 'organisations.show' ? 'active bg-white border-bottom-white' : ''}}"
                                           href="{{route('organisation.show', $organisation)}}">
                                            <span class="material-icons">device_hub</span>
                                            <span>Prozesse</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="container bg-white border border-top-0 p-3 mb-5">

                                @include('partials.processes-sort-search', [
                                    "routeName" => "organisation.show",
                                    "routeParams" => ["organisation" => $organisation]
                                ])

                                <div class="row">
                                    @forelse ($processes as $process)
                                        <div class="col-12 mb-3">
                                            @include ('processes.card')
                                        </div>
                                    @empty
                                        <div class="col">
                                            <span>Noch keine Prozesse vorhanden.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

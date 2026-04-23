<?php

use App\Models\Setting;

/* @var \Illuminate\Support\Collection|\App\Models\Process[] $processes */
/* @var \App\Models\User $user */

?>

@extends ('layouts.app')

@push('header_js')
    <!-- Optionales JS hinzufügen -->
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container">
        <div class="row pt-4">
            <div class="col-lg-3 col-md-4 col-12 float-md-left pr-md-3 pr-xl-6">
                <div class="clearfix mb-4">
                    <div class="col-12 col-md-12 bg-white border rounded">
                        <div class="p-3">
                            <profile-picture :src="'{{$user->imagePath()}}'"
                                             :endpoint="'{{route('api.user.profile_picture')}}'"></profile-picture>
                            <h3 class="text-center mt-3">{{$user->namespace}}</h3>
                            @if($user->bio)
                                <hr/>
                                <p class="text-muted text-center">{{$user->bio}}</p>
                            @endif
                            @if($user->city)
                                <span class="text-muted d-flex justify-content-center">
                                    <span>
                                        <span class="material-icons">place</span>
                                        <span>{{$user->city}}</span>
                                    </span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-12 float-md-left pl-md-3">
                    @includeWhen(!$user->hasDemo() && !Setting::retrieveUser('hide-allisa-promotion', false) && config('allisa.console.enabled'), 'partials.allisa-demo-promotion')
                    @includeWhen(isset($runningSimulations), 'partials.running-simulations')
                    @includeWhen(isset($runningDemos), 'partials.running-demos')
                    <div class="row">
                        <div class="col">
                            <div class="d-flex justify-content-between">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link {{$viewName === 'profile.processes' ? 'active bg-white border-bottom-white' : ''}}"
                                           href="{{route('profile.processes')}}">
                                            <span class="material-icons">device_hub</span>
                                            <span>Prozesse </span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{$viewName === 'profile.solutions' ? 'active bg-white border-bottom-white' : ''}}"
                                           href="{{route('profile.solutions')}}">
                                            <span class="material-icons">star</span>
                                            <span>Lösungen</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{$viewName === 'profile.licenses' ? 'active bg-white border-bottom-white' : ''}}"
                                           href="{{route('profile.licenses')}}">
                                            <span class="material-icons">balance</span>
                                            <span>Lizenzen</span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="nav nav-tabs flex-grow-1 justify-content-end">
                                    @if($viewName === 'profile.processes')
                                    <li class="nav-item">
                                        <a class="nav-link bg-white border text-primary" href="{{ route('process.create', ['namespace' => $user->namespace]) }}">
                                            <span class="material-icons">add</span>
                                            <span>Prozess erstellen</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if($viewName === 'profile.solutions')
                                        <li class="nav-item">
                                            <a class="nav-link bg-white border text-primary" href="{{route('solution.create', ['namespace' => $user->namespace])}}">
                                                <span class="material-icons">add</span>
                                                <span>Lösung erstellen</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @yield('profile.content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

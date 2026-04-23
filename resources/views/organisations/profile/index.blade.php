<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\Role $role */

$canPlatformsManage = $role?->can('platforms.manage');
$canOrganisationManage = $role?->can('organisation.manage');

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
                            <profile-picture :src="'{{$organisation->imagePath()}}'"
                                             :editable="{{ json_encode($canOrganisationManage) }}"
                                             :endpoint="'{{route('api.organisation.profile_picture', $organisation)}}'"></profile-picture>
                            <h3 class="text-center mt-3 {{str_contains($organisation->name, ' ') ?  '' : 'text-truncate'}}" title="{{$organisation->name}}">{{$organisation->name}}</h3>
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
            <div class="col-lg-9 col-md-8 col-12 float-md-left pl-md-3">
                @includeWhen(isset($runningSimulations), 'partials.running-simulations')
                @includeWhen(isset($runningDemos), 'partials.running-demos')
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link {{$viewName === 'organisations.profile.processes' ? 'active bg-white border-bottom-white' : ''}}"
                                       href="{{route('organisation.processes', $organisation)}}">
                                        <span class="material-icons">device_hub</span>
                                        <span>Prozesse</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{$viewName === 'organisations.profile.solutions' ? 'active bg-white border-bottom-white' : ''}}"
                                       href="{{route('organisation.solutions', $organisation)}}">
                                        <span class="material-icons">star</span>
                                        <span>Lösungen</span>
                                    </a>
                                </li>
                                @can('viewLicenses', $organisation)
                                    <li class="nav-item">
                                        <a class="nav-link {{$viewName === 'organisations.profile.licenses' ? 'active bg-white border-bottom-white' : ''}}"
                                           href="{{route('organisation.licenses', $organisation)}}">
                                            <span class="material-icons">balance</span>
                                            <span>Lizenzen</span>
                                        </a>
                                    </li>
                                @endif
                                @can('viewMembers', $organisation)
                                    <li class="nav-item">
                                        <a class="nav-link {{$viewName === 'organisations.profile.members' ? 'active bg-white border-bottom-white' : ''}}"
                                           href="{{route('organisation.members', $organisation)}}"><span
                                                    class="material-icons">groups</span> Mitglieder</a>
                                    </li>
                                @endif
                                @if($canPlatformsManage || $canOrganisationManage)
                                    @if($canOrganisationManage)
                                        <li class="nav-item">
                                            <a class="nav-link"
                                               href="{{route('organisation.settings.data', $organisation)}}">
                                                <span class="material-icons">settings</span>
                                            </a>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <a class="nav-link"
                                               href="{{route('organisation.settings.systems', $organisation)}}">
                                                <span class="material-icons">settings</span>
                                            </a>
                                        </li>
                                    @endif
                                @endcan
                            </ul>
                            <ul class="nav nav-tabs flex-grow-1 justify-content-end">
                                @if($role?->can('processes.create') && $viewName === 'organisations.profile.processes')
                                    <li class="nav-item">
                                        <a class="nav-link bg-white border text-primary" href="{{ route('process.create', ['namespace' => $organisation->namespace]) }}">
                                            <span class="material-icons">add</span>
                                            <span>Prozess erstellen</span>
                                        </a>
                                    </li>
                                @endif
                                @if($role?->can('solutions.create') && $viewName === 'organisations.profile.solutions')
                                    <li class="nav-item">
                                        <a class="nav-link bg-white border text-primary" href="{{route('solution.create', ['namespace' => $organisation->namespace])}}">
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

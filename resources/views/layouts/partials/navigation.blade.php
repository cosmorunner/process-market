<?php

use App\Models\Organisation;

?>

<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom p-1">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="/img/logo.png" id="logo-img" title="Prozessfabrik Logo" alt="Prozessfabrik Logo"
                 style="max-height:25px;"/>
            <span class="text-primary">{{ config('app.name') }}</span>
        </a>
        @if(!isset($blankNavigation) || !$blankNavigation)
            <button class="navbar-toggler p-0 custom-navbar-font-color mr-2" type="button" data-toggle="collapse"
                    data-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false"
                    aria-label="Hauptnavigation">
                    <span class="custom-navbar-font-color">
                        <span class="material-icons mi-2x" aria-hidden="true">menu</span>
                    </span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav nav-pills mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="/docs/de/overview">Dokumentation</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    <li class="nav-item mx-3">
                        <a class="nav-link text-primary"
                           href="{{route('process.create', ['namespace' => auth()->user() ? auth()->user()->namespace : ''])}}">Neuer
                            Prozess</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('auth.login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('auth.register') }}</a>
                            </li>
                        @endif
                    @else
                        <!-- Desktop -->
                        <li class="nav-item dropdown dropdown-hover rounded align-items-center d-sm-none d-md-block">
                            @if(!$user->contextModel)
                                <a id="navbarDropdown" class="nav-link d-block px-2 py-0"
                                   href="{{$user->profileProcessesPath()}}" role="button" aria-haspopup="true"
                                   aria-expanded="false">
                                    <span class="d-inline-block text-primary font-weight-bold">
                                        {{$user->namespace}}
                                    </span>
                                    <small class="text-muted d-block">
                                        Eigenes Profil
                                    </small>
                                </a>
                            @else
                                <a id="navbarDropdown" class="nav-link d-block px-2 py-0"
                                   href="{{route('organisation.processes', ['organisation' => $user->contextModel->namespace])}}"
                                   role="button" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-inline-block text-primary font-weight-bold">
                                        {{ $user->contextModel->name }}
                                    </span>
                                    <small class="text-muted d-block">
                                        {{$user->contextModel->roleOf($user)->name}}
                                    </small>
                                </a>
                            @endif
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                @if($user->organisations->isNotEmpty())
                                    <button class="dropdown-item py-1" type="button" data-toggle="modal"
                                            data-target="#switchUserContext">
                                        Organisationen
                                    </button>
                                    <div class="dropdown-divider"></div>
                                @endif
                                    @if($user->contextModel !== null)
                                    <a class="dropdown-item py-1" href="#"
                                       onclick="event.preventDefault(); document.getElementById('updateUserContextForm').submit();">
                                        Eigenes Profil
                                    </a>
                                        <form id="updateUserContextForm" class="d-none"
                                              action="{{ route('update_user_context') }}" method="post">
                                        @method('PATCH')
                                        @csrf
                                        <input type="hidden" name="context" value="{{ null }}">
                                    </form>
                                    <div class="dropdown-divider"></div>
                                @endif
                                @if($user->hasDemo())
                                    <a class="dropdown-item py-1" target="_blank"
                                       href="{{ sprintf(config('allisa.cloud_host'), $user->demo_identifier) }}">
                                        <span>Allisa Plattform Demo</span>
                                        <span class="material-icons">open_in_new</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif
                                <a class="dropdown-item py-1" href="{{route('settings.data')}}">Einstellungen</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item py-1 text-muted" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    {{ __('auth.logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        <!-- Mobile -->
                        <li class="nav-item align-items-center d-none d-sm-block d-md-none">
                            <a id="navbarDropdown" class="nav-link" href="{{route('profile.processes')}}" role="button"
                               aria-haspopup="true" aria-expanded="false">
                                {{ $user->namespace }}
                            </a>
                            @if($user->organisations->isNotEmpty())
                                <ul class="list-group list-group-flush">
                                    @foreach($user->organisations->sort(fn(Organisation $a, Organisation $b) => $a->name > $b->name ? 1 : 0) as $organisation)
                                        <li class="list-group-item p-1">
                                            <a href="{{$organisation->profileProcessesPath()}}">{{$organisation->name}}</a>
                                        </li>
                                    @endforeach
                                    @if($user->hasDemo())
                                        <li class="list-group-item p-1">
                                            <a href="{{ sprintf(config('allisa.cloud_host'), $user->demo_identifier) }}">
                                                <span>Allisa Plattform Demo</span>
                                                <span class="material-icons">open_in_new</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="list-group p-1">
                                        <a href="{{route('settings.data')}}">Einstellungen</a>
                                    </li>
                                    <li class="list-group p-1">
                                        <a class="text-muted" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                            {{ __('auth.logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            @endif
                        </li>
                    @endguest
                </ul>
            </div>
        @endif
    </div>
</nav>

@auth
    @include('organisations.switch-user-context', ['user' => $user])
@endauth
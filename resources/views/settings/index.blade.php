@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row pt-4 mb-3">
            <div class="col">
                <a href="{{route('profile.processes')}}">Zum Profil</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h3>Einstellungen</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div>
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'settings.data' ? 'active bg-white border-bottom-white' : ''}}" href="{{route('settings.data')}}"><span class="material-icons">info</span> Daten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'settings.organisations' ? 'active bg-white border-bottom-white' : ''}}" href="{{route('settings.organisations')}}"><span class="material-icons">group</span> Organisationen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'settings.systems' ? 'active bg-white border-bottom-white' : ''}}" href="{{route('settings.systems')}}"><span class="material-icons">account_tree</span> Allisa Plattformen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'settings.security' ? 'active bg-white border-bottom-white' : ''}}" href="{{route('settings.security')}}"><span class="material-icons">lock</span> Sicherheit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$viewName === 'settings.account' ? 'active bg-white border-bottom-white' : ''}}" href="{{route('settings.account')}}"><span class="material-icons">account_circle</span> Konto</a>
                        </li>
                    </ul>
                </div>

                @yield('settings.content')

            </div>
        </div>
    </div>
@endsection

<?php

/* @var \App\Models\Organisation $organisation */

?>

@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{route('organisation.processes', $organisation)}}">Zum Profil</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h3>{{$organisation->name}} - Einstellungen</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div>
                    <ul class="nav nav-tabs">
                        @can('manageAccount', $organisation)
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'organisations.settings.data' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('organisation.settings.data', $organisation)}}">
                                    <span class="material-icons">info</span> Daten
                                </a>
                            </li>
                        @endif
                        @can('managePlatforms', $organisation)
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'organisations.settings.systems' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('organisation.settings.systems', $organisation)}}">
                                    <span class="material-icons">account_tree</span> Allisa Plattformen
                                </a>
                            </li>
                        @endcan
                        @can('manageAccount', $organisation)
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'organisations.settings.account' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('organisation.settings.account', $organisation)}}">
                                    <span class="material-icons">admin_panel_settings</span> Konto
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>

                @yield('settings.content')

            </div>
        </div>
    </div>
@endsection

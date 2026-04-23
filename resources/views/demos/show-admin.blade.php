<?php

/**
 * @var App\Models\Solution $solution
 * @var App\Models\Demo $demo
 */

?>

@extends ('layouts.app')

@push('header_js')
    <!-- Optionales JS hinzufügen -->
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row py-2 bg-light shadow-sm">
            <div class="col d-flex align-items-center">
                <a class="btn btn-sm btn-success" href="{{route('solution.config', $solution)}}">
                    <span class="material-icons">arrow_back</span>
                    <span>Zurück</span>
                </a>
                <span class="text-muted mx-3">{{$solution->name}} - {{$demo->solutionVersion->full_namespace}}</span>
                <span class="badge badge-danger">
                    <span class="material-icons">fiber_manual_record</span>
                    <span>Admin-Konfiguration</span>
                </span>
                <small class="d-inline-block text-muted ml-2">Benutzer-Demos basieren auf dieser Allisa Plattform Konfiguration.</small>
            </div>
        </div>
    </div>
    <div class="demo-max-vh border-bottom">
        <iframe width="100%" height="100%" src="{{$magicLink}}" style="border:0" allowfullscreen></iframe>
    </div>
@endsection

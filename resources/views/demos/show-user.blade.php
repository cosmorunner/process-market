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
                <form method="POST" class="d-inline-block"
                      action="{{route('demo.delete', ['demo' => $demo])}}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ref" value="{{request('ref')}}"/>
                    <button type="submit" class="btn btn-sm btn-danger"><span class="material-icons">stop</span>
                        Beenden
                    </button>
                </form>
                <span class="text-muted mx-3">{{$solution->name}} - {{$demo->solutionVersion->full_namespace}}</span>
                <span class="badge badge-success">
                    <span class="material-icons">fiber_manual_record</span>
                    <span>Live Benutzer-Demo</span>
                </span>
            </div>
        </div>
    </div>
    <div class="demo-max-vh border-bottom">
        <iframe width="100%" height="100%" src="{{$magicLink}}" style="border:0" allowfullscreen></iframe>
    </div>
@endsection

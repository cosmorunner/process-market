<?php

/**
 * @var App\Models\Process $process
 * @var App\Models\Simulation $simulation
 * @var App\Models\Environment $environment
 * @var \App\ProcessType\Definition $definition
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
                <span class="mr-2">
                    <span class="text-muted">{{$process->title}} ({{$simulation->processVersion->full_namespace}})</span>
                    @if($simulation->environment)
                        <span class="text-muted">- {{$simulation->environment->name}}</span>
                   @endif
                </span>
                <switch-simulation-user :simulation-id="'{{$simulation->id}}'"
                                        :allisa-id-prop="'{{$simulation->allisa_id}}'" :users="{{json_encode($users)}}"
                                        :roles="{{json_encode($definition->roles->toArray())}}"
                                        :public-role-id="'{{$definition->public_role_id ?? ''}}'"
                                        :allisa-demo-user-id="'{{config('allisa.simulation.user_id')}}'"
                                        :allisa-user-id="'{{ $simulation->allisa_user_id}}'"
                                        :blueprint="{{json_encode($blueprint ?? '')}}"></switch-simulation-user>
                @if($displayConfigLink)
                    <a class="mx-3 text-muted" href="{{$process->configPath($simulation->processVersion->version)}}">Konfiguration</a>
                @endif
                @if($simulation->user_id === auth()->user()->id)
                    <form method="POST" class="mr-3 d-inline-block"
                          action="{{route('simulation.delete', ['simulation' => $simulation])}}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ref" value="{{request('ref')}}"/>
                        @if($displayDevelopLink)
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{$process->devPath($simulation->processVersion->version)}}">
                                <span class="material-icons">open_in_new</span>
                                <span> Zu Regeln & Daten</span>
                            </a>
                        @endif
                        <button type="submit" class="btn btn-sm btn-danger ml-2">
                            <span class="material-icons">stop</span>
                            <span>Stop</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="demo-max-vh border-bottom">
        <iframe width="100%" height="100%" src="{{$magicLink}}" style="border:0" allowfullscreen></iframe>
    </div>
@endsection

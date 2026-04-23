<?php

use App\Http\Resources\Process as ProcessResource;
use App\Http\Resources\ProcessVersion as ProcessVersionResource;

/**
 * @var App\Models\Process $process
 * @var App\Models\ProcessVersion $processVersion
 * @var App\Models\User $user
 */

?>

@extends ('layouts.design')

@push('header_js')
    <script src="{{ mix('js/config-app.js') }}" defer></script>
@endpush

@section('content')
    <config-app :author-profile-path="'{{ $process->authorProfileProcessesPath() }}'"
                :process-prop="{{json_encode(new ProcessResource($process))}}"
                :process-version="{{json_encode(new ProcessVersionResource($processVersion))}}"
                :author-name="'{{$process->author->name}}'"
                :default-allisa-process-id="'{{config('allisa.simulation.process_id')}}'"
                :default-allisa-user-identity-id="'{{config('allisa.simulation.user_identity_id')}}'"
                :environments-prop="{{json_encode($environments)}}" :simulation-id="'{{$simulation->id ?? ''}}'"
                :user-settings="{{json_encode($userSettings)}}" :can-update-version="{{json_encode($canUpdateVersion)}}"
                :can-update-process="{{json_encode($canUpdateProcess)}}"
                :can-complete-version="{{json_encode($canCompleteVersion)}}"
                :plugins="{{json_encode($plugins)}}" :enabled-undo="{{json_encode($enableUndo)}}"
                :enabled-redo="{{json_encode($enableRedo)}}" :urls="{{json_encode($urls)}}"></config-app>
@endsection

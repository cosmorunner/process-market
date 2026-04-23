<?php

use App\Models\Organisation;
use App\Http\Resources\Process as ProcessResource;
use App\Http\Resources\ProcessVersion as ProcessVersionResource;

/**
 * @var App\Models\Process $process
 * @var App\Models\ProcessVersion $processVersion
 */

?>

@extends ('layouts.design')

@push('header_js')
    <script src="{{ mix('js/develop-app.js') }}" defer></script>
@endpush
@section('content')
    <div class="position-relative">
        <div id="graph-app">
            <div class="container-fluid position-relative graph-wrapper">
                <develop-app :process-prop="{{json_encode(new ProcessResource($process))}}"
                             :process-version="{{json_encode(new ProcessVersionResource($processVersion))}}"
                             :author-name="'{{$process->author->namespace}}'"
                             :user-id="'{{auth()->user()->id}}'"
                             :author-profile-path="'{{$process->authorProfileProcessesPath()}}'"
                             :organisation-id="'{{$process->author instanceof Organisation ? $process->author->id : null}}'"
                             :environments-prop="{{json_encode($processVersion->environments()->oldest()->get())}}"
                             :urls="{{json_encode($urls)}}" :version="'{{$processVersion->version}}'"
                             :running-simulation="{{json_encode($simulationResource)}}"
                             :can-update-version="{{json_encode($canUpdateVersion)}}"
                             :can-update-process="{{json_encode($canUpdateProcess)}}"
                             :can-complete-version="{{json_encode($canCompleteVersion)}}"
                             :enabled-undo="{{json_encode($enableUndo)}}"
                             :enabled-redo="{{json_encode($enableRedo)}}"
                             :sidebar-collapse-prop="{{json_encode($sidebarCollapse)}}"
                             :allisa-demo-user-id="'{{$allisaDemoUserId}}'"
                             :allisa-demo-identity-id="'{{$allisaDemoIdentityId}}'"></develop-app>
            </div>
        </div>
    </div>
@endsection

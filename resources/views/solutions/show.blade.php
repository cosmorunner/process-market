@extends ('layouts.app')

@push('header_js')
<script src="{{ mix('js/develop-app.js') }}" defer></script>
@endpush

<?php
/**
 * @var App\Models\Process $process
 * @var App\Models\User $user
 */
$user = auth()->user();
?>

@section('content')
    <div id="graph-app">
        <cytoscape-app
            :editable="{{ json_encode($user && $user->can('update', $process)) }}"
            :editing="false"
            :process="{{json_encode($process)}}"
            :author-name="'{{$process->author->name}}'"
            :author-path="'{{$process->author->publicPath()}}'"
            :graph-id="'{{$processVersion->id}}'"
            :graph-definition="{{json_encode($processVersion->definition)}}"
            :graph-calculated="{{json_encode($processVersion->calculated)}}"
            :version="'{{$processVersion->version}}'"
            :running-simulation="{{json_encode(null)}}"
        >
        </cytoscape-app>
    </div>
@endsection

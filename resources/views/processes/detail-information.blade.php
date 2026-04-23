<?php
/**
 * @var App\Models\Process $process
 */
?>

@extends('processes.detail')

@section('processes.detail.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                @if($process->description)
                    <p class="text-muted">{{$process->description}}</p>
                @else
                    <p class="text-muted">
                        <i>Keine Beschreibung</i>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection

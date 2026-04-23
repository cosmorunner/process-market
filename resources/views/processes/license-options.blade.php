<?php
/* @var \App\Models\Process $process */
?>

@extends('processes.edit')

@section('processes.edit.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col-12">
                <license-settings :process-id="'{{ $process->id }}'"
                                  :license="{{json_encode($process->license)}}"
                                  :has-published-version="{{$hasPublishedVersion ? 'true' : 'false'}}">
                </license-settings>
            </div>
        </div>
    </div>
@endsection

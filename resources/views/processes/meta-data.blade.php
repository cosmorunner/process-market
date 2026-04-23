<?php
/* @var App\Models\Process $process */
?>


@extends('processes.edit')

@section('processes.edit.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-8">
                <update-process
                    :process-prop="{{json_encode($process)}}" :editable="{{json_encode(!$process->isArchived())}}"
                    :has-published-version="{{$hasPublishedVersion ? 'true' : 'false'}}"
                    :urls="{{json_encode($urls)}}"
                    :locals="{{json_encode(['undo' => trans_choice('app.undo', 2), 'notices' => trans_choice('app.notices', 2), 'warnings' => trans_choice('app.warnings', 2), 'info' => trans_choice('app.notices', 2), 'errors' => trans_choice('app.errors', 2)])}}"
                >
                </update-process>
            </div>
        </div>
    </div>
@endsection

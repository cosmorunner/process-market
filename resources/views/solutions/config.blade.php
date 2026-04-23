<?php

/* @var \App\Models\Solution $solution */
/* @var \App\Models\SolutionVersion $solutionVersion */
/* @var array $graphMetas */

?>

@extends('solutions.edit')

@section('solutions.edit.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-8">
                <update-solution-config
                    :solution-prop="{{json_encode($solution)}}"
                    :solution-version-prop="{{json_encode($solutionVersion)}}"
                    :process-types-prop="{{json_encode($processTypes)}}"
                    :has-published-version="{{$hasPublishedVersion ? 'true' : 'false'}}"
                    :urls="{{json_encode($urls)}}"
                    :editable="{{json_encode($canUpdateSolution)}}"
                    :locals="{{json_encode(['undo' => trans_choice('app.undo', 2), 'notices' => trans_choice('app.notices', 2), 'warnings' => trans_choice('app.warnings', 2), 'info' => trans_choice('app.notices', 2), 'errors' => trans_choice('app.errors', 2)])}}"
                >
                </update-solution-config>
            </div>
            @if($canUpdateSolution)
                <div class="col-12 col-md-3 col-lg-4 d-flex justify-content-end align-items-start">
                    <a href="{{route('solution.complete', $solution)}}" class="btn btn-sm btn-success">Version fertigstellen</a>
                </div>
            @endif
        </div>
    </div>
@endsection

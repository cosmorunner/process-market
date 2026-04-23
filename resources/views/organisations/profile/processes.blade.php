<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\Role $role */
/* @var \Illuminate\Support\Collection|\App\Models\Simulation[] $runningSimulations */

?>

@extends('organisations.profile.index')

@section('profile.content')
    <div class="container bg-white border border-top-0 p-3 mb-5">
        @include('partials.processes-sort-search', [
            'routeName' => 'organisation.processes',
            'routeParams' => ['organisation' => $organisation->namespace]
        ])

        <div class="row">
            @foreach($processes as $process)
                <div class="col-12 mb-3">
                    @include('processes.card-on-organisation-profile')
                </div>
            @endforeach
        </div>
    </div>
@endsection

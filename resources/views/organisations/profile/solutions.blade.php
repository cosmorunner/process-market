<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\Role $role */
/* @var \Illuminate\Support\Collection|\App\Models\Solution[] $solutions */
/* @var \Illuminate\Support\Collection|\App\Models\Demo[] $runningDemos */

?>

@extends('organisations.profile.index')

@section('profile.content')
    <div class="container bg-white border border-top-0 p-3 mb-5">
        @if($solutions->isNotEmpty())
            <div class="row">
                @foreach($solutions as $solution)
                    <div class="col-12 mb-3">
                        @include('solutions.card-on-organisation-profile')
                    </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <p>Noch keine Lösung angelegt.</p>
                </div>
            </div>
        @endif
    </div>
@endsection

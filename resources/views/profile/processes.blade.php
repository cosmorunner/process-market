<?php

/* @var \Illuminate\Support\Collection|\App\Models\Process[] $processes */

?>

@extends('profile')

@section('profile.content')
    <div class="container bg-white border border-top-0 p-3 mb-5">

        @include('partials.processes-sort-search', ['routeName' => 'profile.processes'])

        @if($processes->isNotEmpty())
            <div class="row">
                @foreach($processes as $process)
                    <div class="col-12 mb-3">
                        @include ('processes.card-on-profile')
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

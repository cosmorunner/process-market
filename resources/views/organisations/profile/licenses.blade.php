<?php

/* @var \Illuminate\Support\Collection|\App\Models\License[] $licenses */
/* @var \Illuminate\Support\Collection|\App\Models\ProcessLicense[] $processLicenses */
/* @var \Illuminate\Support\Collection|\App\Models\SolutionLicense[] $solutionLicenses */
/* @var \App\Models\Role $role */

?>

@extends('organisations.profile.index')

@section('profile.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <div>
                    <span class="material-icons text-secondary">filter_alt</span>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a href="{{ route('organisation.licenses', ['organisation' => $organisation,  'f' => 'processes']) }}"
                           type="button"
                           class="btn btn-sm btn-outline-secondary @if($filter === 'processes') {{'active'}} @endif">Prozesse</a>
                        <a href="{{ route('organisation.licenses', ['organisation' => $organisation,  'f' => 'solutions']) }}"
                           type="button"
                           class="btn btn-sm btn-outline-secondary @if($filter === 'solutions') {{'active'}} @endif">Lösungen</a>
                    </div>
                </div>
            </div>
        </div>
        @if($filter === 'processes')
            <div class="row">
                @foreach($processLicenses as $license)
                    <div class="col-12 mb-3">
                        @include ('organisations.profile.process-license-on-profile')
                    </div>
                @endforeach
            </div>
            @if($processLicenses->isEmpty())
                <div class="row">
                    <div class="col-12">
                        <span>Noch keine Lizenzen für Prozesse erworben.</span>
                    </div>
                </div>
            @endif
        @endif
        @if($filter === 'solutions')
            <div class="row">
                @foreach($solutionLicenses as $license)
                    <div class="col-12 mb-3">
                        @include ('organisations.profile.solution-license-on-profile')
                    </div>
                @endforeach
            </div>
            @if($solutionLicenses->isEmpty())
                <div class="row">
                    <div class="col-12">
                        <span>Noch keine Lizenzen für Lösungen erworben.</span>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container pt-4">
        <div class="row mb-2">
            <div class="col">
                <h1>Entdecken</h1>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <explore-search></explore-search>
            </div>
        </div>
        <div class="row mb-2">
            @forelse ($processes as $process)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    @include ('processes.card')
                </div>
            @empty
                <div>Keine Prozesse gefunden.</div>
            @endforelse
        </div>
        <hr>
    </div>
@endsection

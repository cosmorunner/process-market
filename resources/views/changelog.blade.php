@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row pt-4 mb-3">
            <div class="col">
                <a href="{{route('settings.data')}}">Zum den Einstellungen</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h3>Changelog</h3>
                <span class="text-muted">{{$version ? 'Aktuelle Version: ' . $version : ''}}</span>
            </div>
        </div>
        <div class="row justify-content-center mb-5">
            <div class="col">
                <div class="card p-4">
                    <div class="mt-2 py-2">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

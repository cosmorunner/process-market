<?php
/* @var App\Models\Process $process */
/* @var App\Models\ProcessVersion $processVersion */
?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container pt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ url()->previous() }}">Zurück</a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12 d-flex justify-content-between">
                <div>
                    <h3>{{$process->title}}</h3>
                    <span class="text-muted">{{$process->full_namespace}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div>
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active bg-white border-bottom-white" href="#">Wiederherstellen</a>
                        </li>
                    </ul>
                </div>
                <div class="container bg-white border border-top-0 p-3">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning" role="alert">
                                Die aktuelle "In der Entwicklung" Version wird auf die am {{$processVersion->published_at->format('d.m.Y')}}
                                fertiggestellte Version {{$processVersion->version}} zurückgesetzt.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-4">
                            <h4 class="text-danger">Prozess-Version wiederherstellen</h4>
                            <p class="text-muted mb-2"><span class="material-icons">error_outline</span> Dies kann nicht rückgängig gemacht werden.</p>
                        </div>
                        <div class="col-sm-12 col-md-8">
                            <div class="container px-0">
                                <form id="form-update-password" role="form" method="POST"
                                      action="{{route('process_version.rollback', ['processVersion' => $processVersion])}}"
                                      novalidate>
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <label for="accept" class="control-label">&nbsp;</label>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="accept" class="custom-control-input" id="accept">
                                                    <label class="custom-control-label" for="accept">Sind Sie sicher?</label>
                                                </div>
                                                @foreach ($errors->{ $bag ?? 'default' }->get('accept') as $error)
                                                    <div class="invalid-feedback d-block">{{$error}}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <span>Version {{$processVersion->version}} wiederherstellen</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

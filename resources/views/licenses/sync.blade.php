<?php
/* @var App\Models\Synchronization $lastSynchronization */
/* @var App\Models\ProcessVersion $processVersion */
/* @var App\Models\System[]|\Illuminate\Support\Collection $systems */
?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{route('license.versions', $license)}}">Zu den Versionen</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h3>Export von {{$processVersion->full_namespace}}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @if($systems->isNotEmpty())
                    <div class="container bg-white border p-3">
                        <div class="row">
                            <div class="col">
                                @if($errors->has('system_ids'))
                                    @foreach($errors->get('system_ids') as $message)
                                        <div class="alert alert-danger" role="alert">
                                            {{$message}}
                                        </div>
                                    @endforeach
                                @endif
                                <p class="text-muted">Die Prozess-Version wird in die Allisa-Plattform importiert.</p>
                                <form method="POST" action="{{ route('license.execute_sync', ['license' => $license, 'version' => $processVersion->version]) }}">
                                    @csrf
                                    <ul class="list-group mb-3">
                                        @foreach($systems as $system)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <span class="d-block">{{$system->name}}</span>
                                                        <small class="d-block text-muted">{{$system->url}}</small>
                                                    </div>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               name="system_ids[]" value="{{$system->id}}"
                                                               id="{{$system->id}}">
                                                        <label class="custom-control-label"
                                                               for="{{$system->id}}"></label>
                                                    </div>
                                                </div>
                                                @if($lastSynchronization = $system->synchronizationsOf($processVersion)->first())
                                                    @if($lastSynchronization->isSuccess())
                                                        <small class="text-success">Erfolgreich exportiert
                                                            am {{\Carbon\Carbon::createFromTimeString($lastSynchronization->created_at)->format('d.m.Y')}}</small>
                                                    @else
                                                        <small class="text-danger">Fehler
                                                            am {{\Carbon\Carbon::createFromTimeString($lastSynchronization->created_at)->format('d.m.Y')}}
                                                            : {{$lastSynchronization->response_message}}</small>
                                                    @endif
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-success">Exportieren
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="container bg-white border border-top-0 p-3">
                        <div class="row mb-3">
                            <div class="col">
                                <a href="{{ $createSystemRoute }}">
                                    <button class="btn btn-sm btn-outline-primary">Allisa Plattform hinzufügen</button>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

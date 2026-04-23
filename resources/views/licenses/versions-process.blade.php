<?php
/* @var App\Models\Process $process */
/* @var App\Models\License $license */
/* @var App\Models\Synchronization $synchronisation */
?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ $license->ownerProfileLicensesPath(['f' => 'processes']) }}">Zurück zu Lizenzen</a>
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
                <div class="container bg-white border border-bottom-0">
                    <div class="row">
                        <div class="col px-4 py-3">
                            @if($license->isOpenSource())
                                <span class="material-icons text-danger">favorite</span>
                                <span>{{$license->typeTitle()}} Lizenz - Erworben am {{$license->created_at->format('d.m.Y')}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="container bg-white border p-3">
                    <div class="row">
                        <div class="col">
                            <table class="table table-sm m-0">
                                <tbody>
                                @foreach($process->versions->sortByDesc('created_at') as $processVersion)
                                    <tr class="d-flex">
                                        <td class="p-2 col-6 @if ($loop->first) border-0 @endif">
                                            <span>{{$processVersion->version}}</span>
                                            @if($processVersion->id == $process->latest_published_version_id)
                                                <span class="badge badge-primary">latest</span>
                                            @endif
                                            @foreach($processVersion->synchronizationsOfSystems($systems)->where('response_code', '=', \Illuminate\Http\Response::HTTP_OK)->sortByDesc('created_at')->unique('system_id') as $synchronisation)
                                                <a href="{{$synchronisation->system->url}}" target="_blank">
                                            <span class="badge badge-light mr-1" data-toggle="tooltip"
                                                  data-placement="top"
                                                  title="Synchronisiert am {{ $synchronisation->created_at->format('d.m.Y') }}">
                                                <span class="material-icons text-success">done</span>
                                                <span>{{$synchronisation->system->name}}</span>
                                            </span>
                                                </a>
                                            @endforeach
                                            <div>
                                                <span class="mb-0 text-muted" style="white-space: pre-line">{{$processVersion->changelog}}</span>
                                            </div>
                                            @include('partials.process-dependencies', ['dependencies' => $processVersion->definition->dependencies])
                                        </td>
                                        <td class="p-2 col-2 @if($loop->first) border-0 @endif">
                                            @include('partials.complexity-score', ['score' => $processVersion->complexity_score])
                                        </td>
                                        <td class="p-2 col-4 d-flex justify-content-end @if ($loop->first) border-0 @endif">
                                            <div class="d-flex flex-column justify-content-between">
                                                <a href="{{route('license.demo', ['license' => $license, 'version' => $processVersion->version]) . '?ref=' . base64_encode(url()->current())}}" class="d-flex justify-content-end">
                                                    <button class="btn btn-outline-primary px-2 py-0">Demo</button>
                                                </a>
                                                <small class="text-muted text-right">{{ $processVersion->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

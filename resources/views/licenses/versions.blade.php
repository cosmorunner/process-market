<?php
/* @var App\Models\Process $process */
/* @var App\Models\License $license */
?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ $license->ownerProfileLicensesPath() }}">Zurück zu Lizenzen</a>
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
                                @foreach($process->versions as $processVersion)
                                    <tr class="d-flex">
                                        <td class="p-2 col-6 @if ($loop->first) border-0 @endif">
                                            <span>{{$processVersion->version}}</span>
                                            @foreach($processVersion->synchronizationsOfSystems($systems)->where('response_code', '=', \Illuminate\Http\Response::HTTP_OK)->sortByDesc('created_at')->unique('system_id') as $synchronisation)
                                                <a href="{{$synchronisation->system->url}}" target="_blank">
                                            <span class="badge badge-light mr-1" data-toggle="tooltip" data-placement="top"
                                                  title="Synchronisiert am {{ $synchronisation->created_at->format('d.m.Y') }}">
                                                <span class="material-icons text-success">done</span>
                                                <span>{{$synchronisation->system->name}}</span>
                                            </span>
                                                </a>
                                            @endforeach
                                            <div>
                                                <span style="white-space: pre-line">{{$processVersion->changelog}}</span>
                                            </div>
                                            @include('partials.process-dependencies', ['dependencies' => $processVersion->definition->dependencies])
                                        </td>
                                        <td class="p-2 col-2 @if ($loop->first) border-0 @endif">
                                            @if($processVersion->isPublished())
                                                @include('partials.complexity-score', ['score' => $processVersion->complexity_score])
                                            @endif
                                        </td>
                                        <td class="p-2 col-2 @if ($loop->first) border-0 @endif">
                                            <span class="text-muted">{{$processVersion->updated_at->diffForHumans()}}</span>
                                        </td>
                                        <td class="p-2 col-2 @if ($loop->first) border-0 @endif">
                                            <a href="{{route('license.sync', ['license' => $license, 'version' => $processVersion->version])}}">
                                                <button class="btn btn-primary float-right px-2 py-0">Exportieren</button>
                                            </a>
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

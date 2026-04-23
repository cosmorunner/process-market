<?php
/**
 * @var App\Models\Process $process
 */

?>

@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div>
        <div class="container">
            <div class="row mt-5">
                <div class="col d-flex justify-content-between align-items-end">
                    <div>
                        <h3>
                            <span>{{$process->title}}</span>
                        </h3>
                        <span class="text-muted">
                            <span class="d-inline-block mr-2">{{$process->full_namespace . '@' . $process->latest_version}}</span>
                            <span class="d-inline-block mr-2 material-icons">{{$process->visibility === \App\Enums\Visibility::Public->value ? 'public' : 'visibility_off'}}</span>
                            @foreach ($process->tags as $tag)
                                <span class="material-icons" style="color:{{$tag->color}};">lens</span>
                                <span>{{$tag->name}}</span>
                            @endforeach
                            @if($process->template)
                                <span class="d-block">
                                <span class="material-icons">subdirectory_arrow_right</span>
                                <span>basiert auf</span>
                                <a href="{{$process->template->process->publicPath()}}">{{$process->template->process->full_namespace}}</a>
                            </span>
                            @endif
                        </span>
                    </div>
                    <div>
                        @if(!$process->hasNoLicense())
                            @if($process->hasPublicLicense())
                                <a href="{{ route('process.purchase', $process) }}"
                                   class="btn btn-sm btn-outline-success">
                                    <span class="material-icons">add</span>
                                    <span>{{ $process->hasOpenSourceLicense() ? 'Lizenz kostenlos erhalten' : 'Lizenz kaufen' }}</span>
                                </a>
                            @endif
                            <a href="{{ route('process.detail.demo', ['namespace' => $process->namespace, 'identifier' => $process->identifier, 'version' => 'latest']) . '?ref=' . base64_encode(url()->current()) }}"
                               class="btn btn-sm btn-primary">
                                <span class="material-icons">play_arrow</span>
                                <span>Demo starten</span>
                            </a>
                        @else
                            <button class="btn btn-sm btn-outline-dark" disabled>Lizenz auf Anfrage</button>
                            <button class="btn btn-sm btn-outline-primary" disabled>Keine öffentliche Demo verfügbar</button>
                        @endif
                    </div>
                </div>
            </div>
            <hr class="my-3"/>
            <div class="row">
                <div class="col-12">
                    @if($process->isArchived())
                        <div class="alert alert-info" role="alert">
                            Der Prozess ist archiviert und wird nicht mehr gepflegt.
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 bg-white border rounded">
                    <div class="pt-1 pt-md-3 pb-1 pb-md-3">
                        <div class="p-3">
                            <div class="d-flex justify-content-center">
                                <img src="{{$process->author->imagePath()}}" class="rounded-circle" alt="" width="130"
                                     height="130"/>
                            </div>
                            @if($process->author instanceof \App\Models\User)
                                <h3 class="text-center mt-3">{{$process->author->namespace}}</h3>
                            @endif
                            @if($process->author instanceof \App\Models\Organisation)
                                <h3 class="text-center mt-3">{{$process->author->name}}</h3>
                                <h5 class="text-muted text-center">{{$process->author->namespace}}</h5>
                            @endif
                            @if($process->author->bio)
                                <hr/>
                                <p class="text-muted">{{$process->author->bio}}</p>
                            @endif
                            @if($process->author->description)
                                <hr/>
                                <p class="text-muted text-center">{{$process->author->description}}</p>
                            @endif
                            @if($process->author->city)
                                <span class="text-muted d-flex justify-content-center">
                                    <span>
                                        <span class="material-icons">place</span>
                                        <span>{{$process->author->city}}</span>
                                    </span>
                                </span>
                            @endif
                            @if($process->author->link)
                                <span class="text-muted d-flex justify-content-center">
                                    <span>
                                        <span class="material-icons">open_in_new</span>
                                        <a class="text-muted" href="{{$process->author->link}}"
                                           target="_blank">Website</a>
                                    </span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div>
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'processes.detail-information' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('process.detail', ['namespace' => $process->namespace, 'identifier' => $process->identifier])}}">Information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'processes.detail-versions' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('process.detail.versions', ['namespace' => $process->namespace, 'identifier' => $process->identifier])}}">Versionen</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col">
                            @yield('processes.detail.content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

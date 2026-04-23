<?php
/**
 * @var App\Models\Solution $solution
 * @var App\Models\ProcessVersion $processVersion
 * @var App\Models\SolutionVersion|null $solutionVersion
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
                        <h3>{{$solution->name}}</h3>
                        <span class="text-muted">
                            @if($solutionVersion)
                                <span class="d-inline-block mr-2">{{$solutionVersion->full_namespace}}</span>
                            @else
                                <span class="d-inline-block mr-2">{{$solution->full_namespace}}</span>
                            @endif
                            <span
                                class="d-inline-block mr-2 material-icons">{{$solution->visibility === \App\Enums\Visibility::Public->value ? 'public' : 'visibility_off'}}</span>
                            @foreach ($solution->tags as $tag)
                                <span class="material-icons"
                                      style="color:{{$tag->color}};">lens</span>
                                <span>{{$tag->name}}</span>
                            @endforeach
                        </span>
                    </div>
                    @if($solutionVersion)
                        <div>
                            <a href="{{ route('solution.purchase', ['solution' => $solution, 'version' => 'latest']) }}"
                               class="btn btn-sm btn-outline-success">
                                <span class="material-icons">balance</span>
                                <span>Lizenzen</span>
                            </a>
                            <a href="{{ route('solution.detail.demo', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier, 'version' => 'latest']) . '?ref=' . base64_encode(url()->current()) }}"
                               class="btn btn-sm btn-primary">
                                <span class="material-icons">play_arrow</span>
                                <span>Demo starten</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <hr class="my-3"/>
            <div class="row">
                <div class="col-md-3">
                    <div class="pt-1 pt-md-3 pb-1 pb-md-3">
                        <img src="{{$solution->author->imagePath()}}" class="rounded d-block mb-3" alt="" width="150"/>
                        <h3>{{$solution->author->namespace}}</h3>
                        @if($solution->author->bio)
                            <hr/>
                            <p class="text-muted">{{$solution->author->bio}}</p>
                        @endif
                        @if($solution->author->city)
                            <span class="text-muted"><span class="material-icons">place</span> {{$solution->author->city}}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-9">
                    <div>
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'solutions.detail-information' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('solution.detail', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier])}}">Information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$viewName === 'solutions.detail-versions' ? 'active bg-white border-bottom-white' : ''}}"
                                   href="{{route('solution.detail.version', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier])}}">
                                    <span>Versionen</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col">
                            @yield('solutions.detail.content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

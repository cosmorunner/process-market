<?php
/* @var App\Models\User $user */
/* @var App\Models\ProcessLicense $license */
/* @var \Illuminate\Support\Collection $runningSimulationProcessIds */

?>
<div class="card">
    <div class="card-header striped-bg p-2 d-flex justify-content-between border-bottom">
        <span class="text-truncate font-weight-bold">
            <span class="text-truncate">
                <a href="{{route('license.versions', $license)}}">
                    {{shorten((string) $license->resource->title, 50)}}
                </a>
            </span>
            <div>
                <small class="text-muted">
                    @if($license->isOpenSource())
                        <span class="material-icons">favorite</span>
                        <span>{{$license->typeTitle()}}</span>
                    @endif
                </small>
            </div>
        </span>
        <div class="d-flex justify-content-end">
            <a class="d-inline-block px-2"
               href="{{route('license.demo', ['license' => $license, 'version' => 'latest']) . '?ref=' . base64_encode('profile.licenses')}}">Demo</a>
            @if($license->allowsCopy())
                <a class="d-inline-block px-2"
                   href="{{route('process.create', ['template' => $license->resource->full_namespace, 'namespace' => $user->namespace])}}">Als Vorlage...</a>
            @endif
            <a class="d-inline-block px-2" href="{{route('license.sync', ['license' => $license, 'version' => 'latest'])}}">Export</a>
        </div>
    </div>
    @if($license->resource->description || count($license->resource->tags))
        <div class="card-body p-2">
            @if($license->resource->description)
                <p class="card-text mb-2">{{ \Illuminate\Support\Str::limit((string) $license->resource->description, 250) }}</p>
            @endif
            @foreach ($license->resource->tags as $tag)
                <small><span class="material-icons" style="color:{{$tag->color}};">lens</span> {{$tag->name}}</small>
            @endforeach
        </div>
    @endif
    <div class="card-footer d-flex justify-content-between bg-white p-2">
        <small class="text-muted d-block text-truncate align-content-center d-flex">
            <a href="{{$license->resource->author->publicPath()}}">
                <img src="{{$license->resource->author->thumbnailPath()}}" class="d-inline-block rounded-circle mr-1" height="25" alt=""/>
            </a>
            <span>
                <a href="{{$license->resource->author->publicPath()}}" class="text-muted">
                    <span>{{$license->resource->namespace}}</span></a> / {{$license->resource->identifier}}</span>
        </small>
        <div>
            @include('partials.complexity-score', ['score' => $license->resource->latestPublishedVersion->complexity_score])
        </div>
    </div>
    <div class="card-footer bg-white p-2">
        <div>
            <small class="text-muted" title="{{$license->created_at}}">Erworben am {{$license->created_at->format('d.m.Y')}}</small>
        </div>
    </div>
</div>

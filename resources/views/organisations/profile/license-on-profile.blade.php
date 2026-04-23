<?php
/* @var App\Models\Organisation $organisation */
/* @var App\Models\ProcessLicense $license */

?>
<div class="card">
    <div class="card-header striped-bg py-2 d-flex justify-content-between border-bottom">
        <span class="text-truncate font-weight-bold">
            <span class="text-truncate">{{shorten((string) $license->resource->title, 50)}}</span>
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
               href="{{route('process.demo', ['process' => $license->resource, 'version' => 'latest']) . '?ref=' . base64_encode('profile.licenses')}}">Demo</a>
            @if($license->allowsProcessCopy())
                <a class="d-inline-block px-2"
                   href="{{route('process.create', ['template' => $license->resource->full_namespace, 'namespace' => $organisation->namespace])}}">Als
                    Vorlage...</a>
            @endif
            <a class="d-inline-block px-2" href="{{route('license.versions', $license)}}">Export</a>
        </div>
    </div>
    @if($license->resource->description || count($license->resource->tags))
        <div class="card-body py-2">
            @if($license->resource->description)
                <p class="card-text mb-2">{{ \Illuminate\Support\Str::limit((string) $license->resource->description, 250) }}</p>
            @endif
            @foreach ($license->resource->tags as $tag)
                <small><span class="material-icons" style="color:{{$tag->color}};">lens</span> {{$tag->name}}</small>
            @endforeach
        </div>
    @endif
    <div class="card-footer d-flex justify-content-between bg-white py-2">
        <small class="text-muted d-block text-truncate align-content-center d-flex">
            <a href="{{$license->resource->author->publicPath()}}">
                <img src="{{$license->resource->author->thumbnailPath()}}" class="rounded-circle d-inline-block mr-1" height="25" alt=""/>
            </a>
            <span>
                <a href="{{$license->resource->author->publicPath()}}" class="text-muted">
                    <span>{{$license->resource->namespace}}</span></a> / {{$license->resource->identifier}}</span>
        </small>
        <div>
            @include('partials.complexity-score', ['score' => $license->resource->latestPublishedVersion->complexity_score])
        </div>
    </div>
    <div class="card-footer bg-white py-1">
        <div>
            <small class="text-muted" title="{{$license->created_at}}">Erworben
                am {{$license->created_at->format('d.m.Y')}}</small>
        </div>
    </div>
</div>

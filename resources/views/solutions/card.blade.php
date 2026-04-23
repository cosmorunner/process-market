<?php
/* @var App\Models\Solution $solution */
?>
<div class="card h-100">
    <div class="card-header d-flex justify-content-between border-bottom-0 p-2">
        <span class="text-truncate font-weight-bold">
            <a href="{{$solution->detailPath()}}" class="d-inline-block mb-1">{{shorten($solution->name, 50)}}</a>
            <div>
                <small class="text-muted">
                    @if($solution->hasOpenSourceLicense())
                        <span class="material-icons">favorite</span> Open-Source
                    @endif
                </small>
            </div>
        </span>
    </div>
    <div class="card-body p-2 d-flex flex-column justify-content-between">
        @if($solution->description)
            <p class="card-text flex-grow-1 mb-2">{{ \Illuminate\Support\Str::limit((string) $solution->description, 250) }}</p>
        @endif
        <div>
            @foreach ($solution->tags as $tag)
                <small class="text-muted"><span class="material-icons" style="color:{{$tag->color}};">lens</span> {{$tag->name}}</small>
            @endforeach
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between bg-white p-2">
        <small class="text-muted d-block text-truncate align-content-center d-flex">
            <a href="{{$solution->author->publicPath()}}">
                <img src="{{$solution->author->thumbnailPath()}}" class="d-inline-block rounded-circle mr-1" height="25" alt=""/>
            </a>
            <span>
                <a href="{{$solution->author->publicPath()}}" class="text-muted"><span>{{$solution->namespace}}</span></a> / {{$solution->identifier}}</span>
        </small>
        <div>
            @include('partials.complexity-score', ['score' => $solution->latestPublishedVersion->complexity_score])
        </div>
    </div>
</div>

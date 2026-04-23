<?php
/* @var App\Models\Process $process */
?>
<div class="card h-100">
    <div class="card-header d-flex justify-content-between border-bottom-0 p-2">
        <span class="text-truncate font-weight-bold">
            <a href="{{$process->publicPath()}}" class="d-inline-block mb-1">{{shorten($process->title, 50)}}</a>
            <div>
                <small class="text-muted">
                    @if($process->hasOpenSourceLicense())
                        <span class="material-icons">favorite</span> Open-Source
                    @else
                        <span class="material-icons mi-1-25x">local_atm</span> Auf Anfrage
                    @endif
                </small>
            </div>
        </span>
    </div>
    <div class="card-body p-2 d-flex flex-column justify-content-between">
        @if($process->description)
            <p class="card-text flex-grow-1 mb-2">{{ \Illuminate\Support\Str::limit((string) $process->description, 250) }}</p>
        @endif
        <div>
            @foreach ($process->tags as $tag)
                <small class="text-muted"><span class="material-icons"
                                                style="color:{{$tag->color}};">lens</span> {{$tag->name}}</small>
            @endforeach
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between bg-white p-2">
        <small class="text-muted d-block text-truncate align-content-center d-flex">
            <a href="{{$process->author->publicPath()}}">
                <img src="{{$process->author->thumbnailPath()}}" class="d-inline-block rounded-circle mr-1" height="25"
                     alt=""/>
            </a>
            <span>
                <a href="{{$process->author->publicPath()}}" class="text-muted"><span>{{$process->namespace}}</span></a>
                <span> / {{$process->identifier}}</span>
            </span>
        </small>
        <div>
            @include('partials.complexity-score', ['score' => $process->latestPublishedVersion->complexity_score])
        </div>
    </div>
</div>

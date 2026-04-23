<?php
/* @var App\Models\Process $process */
?>

@csrf
<div class="form-group">
    <label for="title">Titel</label>
    <input type="text"
           value="{{old('title') ?? $process->title}}"
           class="form-control {{$errors->{ $bag ?? 'default'}->has('title') ? 'is-invalid' : '' }}"
           name="title"
           aria-describedby="titleHelp"
           placeholder="">
    @foreach ($errors->{ $bag ?? 'default' }->get('title') as $error)
        <div class="invalid-feedback">{{$error}}</div>
    @endforeach
</div>
<div class="form-group">
    <label for="description">Beschreibung</label>
    <textarea
        class="form-control {{$errors->{ $bag ?? 'default'}->has('description') ? 'is-invalid' : '' }}"
        name="description"
        rows="2">{{old('description') ?? $process->description}}</textarea>
    <small class="form-text text-muted">
        Prozesse mit einer Beschreibung werden eher gefunden.
    </small>
    @foreach ($errors->{ $bag ?? 'default' }->get('description') as $error)
        <div class="invalid-feedback">{{$error}}</div>
    @endforeach
</div>
<div class="form-group">
    <label for="title">Tags</label>
    <input type="text"
           value="{{old('tags') ?? $process->tagsToString()}}"
           class="form-control {{$errors->{ $bag ?? 'default'}->has('tags') ? 'is-invalid' : '' }}"
           name="tags"
           aria-describedby="titleHelp"
           placeholder="">
    <small class="form-text text-muted">
        Semikolon separiert.
    </small>
    @foreach ($errors->{ $bag ?? 'default' }->get('tags') as $error)
        <div class="invalid-feedback">{{$error}}</div>
    @endforeach
</div>
@if($displayVisibility)
    <div class="form-group">
        <label for="description">Sichtbarkeit</label>
        <div class="form-check mb-2">
            <input
                class="form-check-input {{$errors->{ $bag ?? 'default'}->has('visibility') ? 'is-invalid' : '' }}"
                type="radio"
                name="visibility"
                id="visibilityPrivate"
                value="0"
                {{$process->visibility === null || $process->visibility === App\Enums\Visibility::Private->value ? 'checked' : ''}}
            >
            <label class="form-check-label" for="visibilityPrivate">
                Privat<span
                    class="text-muted"> - Nur der Eigentümer (Benutzer oder Organisation) kann den Prozess sehen. Der Prozess wird nicht gelistet.</span>
            </label>
        </div>
        <div class="form-check mb-2">
            <input
                class="form-check-input {{$errors->{ $bag ?? 'default'}->has('visibility') ? 'is-invalid' : '' }}"
                type="radio"
                name="visibility"
                id="visibilityHidden"
                value="1"
                {{$process->visibility === App\Enums\Visibility::Hidden->value ? 'checked' : ''}}
                {{!$process->hasPublishedVersion() ? 'disabled' : ''}}
            >
            <label class="form-check-label" for="visibilityHidden">
                Versteckt<span class="text-muted"> - Der Prozess wird nicht gelistet, ist aber per URL öffentlich aufrufbar.</span>
            </label>
        </div>
        <div class="form-check disabled">
            <input
                class="form-check-input {{$errors->{ $bag ?? 'default'}->has('visibility') ? 'is-invalid' : '' }}"
                type="radio"
                name="visibility"
                id="visibilityPublic"
                value="2"
                {{$process->visibility === App\Enums\Visibility::Public->value ? 'checked' : ''}}
                {{!$process->hasPublishedVersion() ? 'disabled' : ''}}
            >
            <label class="form-check-label" for="visibilityPublic">
                Öffentlich<span class="text-muted"> - Der Prozess ist öffentlich sichtbar und wird gelistet.</span>
            </label>

            @foreach ($errors->{ $bag ?? 'default' }->get('visibility') as $error)
                <div class="invalid-feedback">{{$error}}</div>
            @endforeach
        </div>
    </div>
    @if(!$process->hasPublishedVersion())
        <div class="alert alert-info" role="alert">
            Sie müssen zunächst eine Prozess-Version fertigstellen, bevor Sie den Prozess veröffentlichen können.
        </div>
    @endif
@endif
<div class="row">
    <div class="col">
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-sm btn-success">{{$buttonText}}</button>
        </div>
    </div>
</div>

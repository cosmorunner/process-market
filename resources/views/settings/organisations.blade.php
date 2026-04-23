@extends('settings.index')

@section('settings.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row mb-3">
            <div class="col">
                <a href="{{route('organisation.create')}}">
                    <button class="btn btn-sm btn-outline-primary">Neue Organisation erstellen</button>
                </a>
            </div>
        </div>
        <div class="row">
            @forelse($organisations as $organisation)
                <div class="col-12">
                    @foreach ($errors->{ $bag ?? 'default' }->get('accept') as $error)
                        <div class="alert alert-danger" role="alert">
                            Die Organisation benötigt mindestens einen Administrator. Sie können die Organisation in den Konto-Einstellungen der Organisation auflösen.
                        </div>
                    @endforeach
                    <div class="card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between">
                            <div>
                                <a href="{{$organisation->profileProcessesPath()}}">{{$organisation->name}}</a><span class="badge badge-light">{{$organisation->roleOf(Auth::user())->name}}</span>
                                <small class="text-muted d-block">{{$organisation->namespace}}</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-light text-primary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="material-icons mi-1-5x">more_vert</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @can('manageAccount', $organisation)
                                        <a class="dropdown-item" href="{{$organisation->settingsPath()}}">Einstellungen</a>
                                        <div class="dropdown-divider"></div>
                                    @endcan
                                    <form method="POST" action="{{route('organisation.exit', $organisation)}}">
                                        @csrf
                                        <input type="hidden" name="accept" value="on"/>
                                        <button class="dropdown-item text-danger" type="submit">Austreten</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @if($organisation->description)
                            <div class="card-body py-2">
                                <p class="card-text">{{\Illuminate\Support\Str::limit($organisation->description, 200)}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col">Erstellen Sie eine Organisation und entwickeln Sie gemeinsam Prozesse.</div>
            @endforelse
        </div>
    </div>
@endsection

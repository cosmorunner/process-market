@extends('settings.index')

@section('settings.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row mb-3">
            <div class="col">
                <a href="{{route('settings.system.create')}}">
                    <button class="btn btn-sm btn-outline-primary">Allisa Plattform hinzufügen</button>
                </a>
            </div>
        </div>
        <div class="row">
            @forelse($systems as $system)
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">{{$system->name}}</h5>
                                <h6 class="card-subtitle text-muted">
                                    <a href="{{$system->url}}" target="_blank">{{$system->url}}</a>
                                    <span class="material-icons">open_in_new</span>
                                </h6>
                            </div>
                            <form method="POST" action="{{route('settings.system.delete', $system)}}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-light" type="submit">
                                    <span class="material-icons text-danger">delete</span>
                                </button>
                            </form>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Client-Id: {{\Illuminate\Support\Str::limit($system->client_id, 8)}}</p>
                        </div>
                        <div class="card-footer">
                            <span class="text-success">Gültig bis {{\Carbon\Carbon::createFromTimeString($system->expires_at)->format('d.m.Y')}}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">Fügen Sie Allisa Plattformen hinzu, um fertiggestellte Prozess-Versionen dorthin zu exportieren.</div>
            @endforelse
        </div>
    </div>
@endsection

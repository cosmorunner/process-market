@extends('layouts.app')

@section('content')
    <div class="container pt-4">
        <div class="row mb-3 justify-content-center">
            <div class="col-md-8">
                <a href="{{route('organisation.settings.systems', $organisation)}}">Zurück</a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Allisa Plattform hinzufügen</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('organisation.settings.system.store', $organisation) }}">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="url" class="col-md-4 col-form-label text-md-right">URL</label>
                                <div class="col-md-6">
                                    <input id="url" type="text"
                                           class="form-control @error('url') is-invalid @enderror"
                                           name="url" value="{{ old('url') }}" required autofocus>
                                    <small class="text-muted">Die URL der Allisa Plattform.</small>
                                    @error('url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="client_id" class="col-md-4 col-form-label text-md-right">Client-Id</label>
                                <div class="col-md-6">
                                    <input id="client_id" type="text"
                                           class="form-control @error('client_id') is-invalid @enderror"
                                           name="client_id" value="{{ old('client_id') }}" required autofocus>
                                    <small class="text-muted">Erstellen Sie im Administrations-Bereich der Allisa Plattform
                                        unter "API Clients" einen "Client Credentials"-Client und geben Sie hier
                                        die Client-Id und das Client-Secret ein.</small>
                                    @error('client_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="client_id" class="col-md-4 col-form-label text-md-right">Client-Secret</label>
                                <div class="col-md-6">
                                    <input id="client_secret" type="text" class="form-control @error('client_secret') is-invalid @enderror"
                                           name="client_secret" value="{{ old('client_secret') }}" required autofocus>
                                    @error('client_secret')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="client_id" class="col-md-4 col-form-label text-md-right"></label>
                                <div class="col-md-6">
                                    @error('owner_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" value="{{$organisation->id}}" name="owner_id"/>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn float-right btn-primary">Hinzufügen</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

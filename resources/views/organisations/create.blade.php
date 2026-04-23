@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row py-4 justify-content-center">
            <div class="col-md-8">
                <a href="{{route('settings.organisations')}}">Zurück</a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Organisation erstellen</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('organisation.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="namespace" class="col-md-4 col-form-label text-md-right">Namespace</label>
                                <div class="col-md-6">
                                    <input id="namespace" type="text"
                                           class="form-control @error('namespace') is-invalid @enderror"
                                           name="namespace" value="{{ old('namespace') }}" required autocomplete="name"
                                           autofocus>
                                    <small class="text-muted">Der Namespace ist einzigartig und identifiziert eindeutig
                                        die Organisation. Nur a-z, 0-9 oder Bindestrich. Mit einem Buchstaben beginnen.</small>
                                    @error('namespace')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="namespace"
                                       class="col-md-4 col-form-label text-md-right">Beschreibung</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" name="description"
                                              rows="5">{{old('description') ?? ''}}</textarea>
                                    <small class="text-muted">Worum geht es in ihrer Organisation?</small>
                                    @error('description')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="float-right btn btn-primary">Erstellen</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    import Textarea from "../../plugins/Allisa/ActionTypeComponent/Form/v1_0_0/execution/fields/Textarea";

    export default {
        components: {Textarea}
    };
</script>

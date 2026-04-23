@extends('settings.index')

@section('settings.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <h4>Passwort ändern</h4>
            </div>
            <div class="col-sm-12 col-md-8">
                <div class="container px-0">
                    <form id="form-update-password" role="form" method="POST" action="{{route('settings.update_password')}}" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-lg-8">
                                <label for="current_password" class="control-label">Aktuelles
                                    Passwort</label>
                                <div>
                                    <div class="form-group">
                                        <input type="password" class="form-control {{$errors->{ $bag ?? 'default'}->has('current_password') ? 'is-invalid' : '' }}" id="current_password"
                                               name="current_password" placeholder="">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('current_password') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>

                                <label for="password" class="control-label">Neues Passwort</label>
                                <div>
                                    <div class="form-group">
                                        <input type="password" class="form-control {{$errors->{ $bag ?? 'default'}->has('password') ? 'is-invalid' : '' }}" id="password"
                                               name="password" placeholder="">
                                        <small id="passwordHelp" class="form-text text-muted">Mindestens 8
                                            Zeichen.</small>
                                        @foreach ($errors->{ $bag ?? 'default' }->get('password') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>

                                <label for="password_confirmation" class="control-label">Passwort
                                    bestätigen</label>
                                <div>
                                    <div class="form-group">
                                        <input type="password" class="form-control {{$errors->{ $bag ?? 'default'}->has('password_confirmation') ? 'is-invalid' : '' }}"
                                               id="password_confirmation"
                                               name="password_confirmation" placeholder="">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('password_confirmation') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-success">Ändern</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

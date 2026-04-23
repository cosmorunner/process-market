<?php
/* @var \App\Models\Invitation $invitation */

?>

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center pt-4">
            <div class="col-md-10 col-lg-8">
                @if($invitation instanceof \App\Models\Invitation && $invitation->isValid())
                    <div class="card mb-2">
                        <div class="card-header border-success font-weight-bold">
                            Registrieren Sie sich, um die Einladung der Organisation "{{$invitation->resource->name}}" anzunehmen.
                        </div>
                    </div>
                @endif
                @if($invitation instanceof \App\Models\Invitation && $invitation->isInvalid())
                    <div class="card mb-2">
                        <div class="card-header border-warning font-weight-bold">
                            Die Einladung zur Organisation "{{$invitation->resource->name}}" ist abgelaufen. Sie können sich dennoch in der
                            Prozessfabrik registrieren.
                        </div>
                    </div>
                @endif
                @if(\Ramsey\Uuid\Uuid::isValid($invitation ?? '') && is_string($invitation))
                    <div class="card mb-2">
                        <div class="card-header border-info font-weight-bold">
                            Die Einladung existiert nicht mehr.
                        </div>
                    </div>
                @endif
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <img class="d-block mx-auto mb-3 text-center" style="max-width: 350px; max-height: 70px;" src="/img/logo.png" title="Prozessfabrik Logo" alt="Prozessfabrik Logo"/>
                        <h1 class="text-center">{{ config('app.name') }}</h1>
                        <p class="text-muted text-center mb-4 mt-2 pb-1">{{ config('app.description') }}</p>
                        <div class="card">
                            <div class="card-header bg-white py-2 text-primary">
                                <b>{{ __('auth.register') }}</b>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf

                                    @if($invitation instanceof \App\Models\Invitation && $invitation->isValid())
                                        <input type="hidden" name="invitation" value="{{$invitation->id}}"/>
                                    @endif

                                    <div class="form-group mb-1">
                                        <label for="namespace" class="col-form-label text-md-right">Benutzername</label>

                                        <input id="namespace" type="text" class="form-control @error('namespace') is-invalid @enderror"
                                               name="namespace" value="{{ old('namespace') }}" required autocomplete="name" autofocus>
                                        <small class="text-muted">Wird öffentlich angezeigt. Nur a-z, 0-9 oder Bindestrich, zwischen 3-20
                                            Zeichen, mit Buchstaben beginnend.</small>
                                        @error('namespace')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-1">
                                        <label for="email" class="col-form-label text-md-right">{{ __('auth.email') }}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                               name="email"
                                               value="{{ old('email') ?? ($invitation instanceof \App\Models\Invitation ? $invitation->email : null) ?? '' }}"
                                               required autocomplete="email">

                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="password" class="col-form-label text-md-right">{{ __('auth.password') }}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                               name="password" required autocomplete="new-password">
                                        <small class="text-muted">Mind. 8 Zeichen.</small>

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="password_confirmation" class="col-form-label text-md-right">{{ __('auth.confirm_password') }}</label>
                                        <input id="password_confirmation" type="password" class="form-control @error('password_confirm') is-invalid @enderror"
                                               name="password_confirmation" required autocomplete="confirm-password">
                                        @error('password_confirm')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="terms" class="col-form-label text-md-right"></label>

                                        <div class="row">
                                            <div class="col-2">
                                                <input type="checkbox" name="terms" class="form-control"/>
                                            </div>
                                            <div class="col-10 px-0">
                                                <small class="text-muted">Ich akzeptiere die <a
                                                        href="{{route('legal', ['section' => 'terms'])}}" target="_blank">allgemeinen Geschäftsbedingungen</a> und <a href="{{route('legal', ['section' => 'privacy'])}}" target="_blank">Datenschutzerklärung</a>.</small>
                                            </div>
                                        </div>

                                        @error('terms')
                                        <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-0 mt-3">
                                        <button type="submit" class="btn btn-block btn-primary">
                                            {{ __('auth.register') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

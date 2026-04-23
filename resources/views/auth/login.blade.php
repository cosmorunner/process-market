@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center pt-4">
            <div class="col-md-10 col-lg-8">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <img class="d-block mx-auto mb-3 text-center" style="max-width: 350px; max-height: 70px;" src="/img/logo.png" title="Prozessfabrik Logo" alt="Prozessfabrik Logo"/>
                        <h1 class="text-center">{{ config('app.name') }}</h1>
                        <p class="text-muted text-center mb-4 mt-2 pb-1">{{ config('app.description') }}</p>
                        <div class="card">
                            <div class="card-header bg-white py-2 text-primary">
                                <b>{{ __('auth.login') }}</b>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="email" class="col-form-label text-md-right">{{ __('auth.email') }}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? '' }}" required autocomplete="email" autofocus>

                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="col-form-label text-md-right">{{ __('auth.password') }}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" value="">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-2 d-flex justify-content-between">
                                        <div>
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                {{ __('auth.remember_me_?') }}
                                            </label>
                                        </div>
                                        <a class="btn btn-link p-0" href="{{ route('password.request') }}">
                                            {{ __('auth.forgot_password_?') }}
                                        </a>
                                    </div>

                                    <div class="form-group mb-0 mt-3">
                                        <button type="submit" class="btn btn-block btn-primary">
                                            {{ __('auth.login') }}
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

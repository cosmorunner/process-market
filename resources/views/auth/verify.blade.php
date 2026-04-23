@extends('layouts.app')

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('info.verify_email_new_link') }}
                            </div>
                        @endif
                        {{ __('info.verify_email') }}, <a
                            href="{{ route('verification.resend') }}">{{ __('info.verify_email_click_here') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

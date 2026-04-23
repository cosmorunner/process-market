@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-sm-8 col-md-10 mt-5">
                @include('layouts.partials.exception-card', [
                    'exceptionTitle' => __('exceptions.default_message'),
                    'backUrl' => url()->previous(),
                    'enableHomePageLink' => true
                ])
            </div>
        </div>
    </div>
@endsection

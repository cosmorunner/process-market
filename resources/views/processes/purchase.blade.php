@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

<?php
/**
 * @var App\Models\Process $process
 */
?>

@section('content')
    <div>
        <div class="container pt-4">
            <div class="row mb-3 d-flex justify-content-center">
                <div class="col-12 col-lg-8">
                    <a href="{{ $process->publicPath() }}">Zurück</a>
                </div>
            </div>
        </div>
        <purchase-process-license
                :user-namespace="'{{$userNamespace}}'"
                :urls="{{json_encode($urls)}}"
                :organisations="{{json_encode(\App\Http\Resources\Organisation::collection($organisations))}}"
                :process="{{json_encode(new \App\Http\Resources\Process($process))}}"
                :level="'{{$process->hasOpenSourceLicense() ? 'open-source' : ''}}'"
        ></purchase-process-license>
    </div>
@endsection

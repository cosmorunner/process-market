@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

<?php

/**
 * @var App\Models\License $license
 * @var App\Models\ProcessVersion $versionModel
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
$defaultErrors = ($errors?->getBag('default')->toArray()) ?? [];

?>

@section('content')
    <div class="container pt-4">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-sm-8 col-md-6">
                <div class="row mb-3">
                    <div class="col d-flex justify-content-between">
                        <h3>Demo starten</h3>
                        @include('partials.docs', ['article' => 'demo'])
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>{{$versionModel->process->title}}</h5>
                        <span class="text-muted">{{$versionModel->full_namespace}}</span>
                    </div>
                    <div class="card-body">
                        @if($versionModel->definition->roles->isNotEmpty() || ($versionModel->definition->publicRole || $versionModel->definition->defaultRole))
                            <start-process-demo
                                endpoint="{{route('license.store_process_demo', ['license' => $license, 'version' => $versionModel->version])}}"
                                csrf="{{csrf_token()}}"
                                reference-url="{{request()->query('ref')}}"
                                :roles="{{json_encode($versionModel->definition->roles, JSON_HEX_QUOT)}}"
                                :environments="{{json_encode($versionModel->environments, JSON_HEX_QUOT)}}"
                                :errors="{{json_encode($defaultErrors, JSON_HEX_QUOT)}}"
                                :license-id="'{{$license?->id}}'"
                                default-role-id="{{$versionModel->definition->default_role_id}}">
                            </start-process-demo>
                        @else
                            <div class="alert alert-primary mb-0" role="alert">
                                Der Prozess benötigt mindestens eine Prozess-Rolle um eine Demo starten zu können.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

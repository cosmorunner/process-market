@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

<?php

/**
 * @var App\Models\License $license
 * @var App\Models\SolutionVersion $versionModel
 */

?>

@section('content')
    <div class="container pt-4">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-sm-8 col-md-6">
                <div class="row mb-3">
                    <div class="col d-flex justify-content-between">
                        <h3>Live-Demo starten</h3>
                        @include('partials.docs', ['article' => 'demo'])
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>{{$versionModel->solution->name}}</h5>
                        <span class="text-muted">{{$versionModel->full_namespace}}</span>
                    </div>
                    <div class="card-body">
                        <start-solution-demo
                            endpoint="{{route('demo.store', ['solutionVersion' => $versionModel])}}"
                            csrf="{{csrf_token()}}"
                            description="{{$versionModel->solution->description}}"
                            :organisation-id="'{{$organisation?->id}}'"
                            :license-id="'{{$license?->id}}'"
                            reference-url="{{request()->query('ref')}}">
                        </start-solution-demo>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

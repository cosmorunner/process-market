@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

<?php

/**
 * @var App\Models\Solution $solution
 * @var App\Models\SolutionVersion $solutionVersion
 * @var App\Models\Organisation $organisation
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
                        <h5>{{$solutionVersion->solution->name}}</h5>
                        <span class="text-muted">{{$solutionVersion->full_namespace}}</span>
                    </div>
                    <div class="card-body">
                        <start-solution-demo
                            endpoint="{{route('demo.store', ['solutionVersion' => $solutionVersion])}}"
                            csrf="{{csrf_token()}}"
                            description="{{$solutionVersion->solution->description}}"
                            :organisation-id="'{{$organisation?->id}}'"
                            reference-url="{{request()->query('ref')}}">
                        </start-solution-demo>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

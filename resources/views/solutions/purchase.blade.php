@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

<?php
/**
 * @var App\Models\Solution $solution
 */
?>

@section('content')
    <div>
        <div class="container pt-4">
            <div class="row mb-3 d-flex justify-content-center">
                <div class="col-8">
                    <a href="{{ $solution->detailPath() }}">Zurück</a>
                </div>
            </div>
        </div>
        <purchase-solution-license
            :user-namespace="'{{$userNamespace}}'"
            :urls="{{json_encode($urls)}}"
            :organisations="{{json_encode(\App\Http\Resources\Organisation::collection($organisations))}}"
            :latest-public-solution-version="{{json_encode(new \App\Http\Resources\SolutionVersion($latestPublicSolutionVersion))}}"
            :unavailable-version="'{{ $unavailableVersion }}'"
            :query-namespace="'{{ $queryNamespace }}'"
            :solution="{{json_encode(new \App\Http\Resources\Solution($solution))}}"
            :level="'mixed'"
        ></purchase-solution-license>
    </div>
@endsection

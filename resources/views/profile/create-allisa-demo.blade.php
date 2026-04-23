@extends ('layouts.app')

@push('header_js')
    <script src="{{ mix('js/utility-apps.js') }}" defer></script>
@endpush

@section('content')
    <div class="container pt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="row mb-3">
                    <div class="col">
                        <h3>
                            <img src="/img/allisa-ring.png" alt="" height="60" class="d-inline-block mr-3"/>
                            <span>Allisa Plattform kostenlos testen</span>
                        </h3>
                    </div>
                </div>
                <div class="container bg-white border p-3">
                    <create-allisa-demo
                        :identifier-prop="'{{$identifier}}'"
                        :search-endpoint="'{{$searchEndpoint}}'"
                        :store-endpoint="'{{$storeEndpoint}}'"
                        :systems-route="'{{$systemsRoute}}'"
                        :profile-route="'{{$profileRoute}}'"
                        :allisa-privacy="'{{$allisaPrivacy}}'"
                        :allisa-terms="'{{$allisaTerms}}'"
                    ></create-allisa-demo>
                </div>
            </div>
        </div>
    </div>
@endsection

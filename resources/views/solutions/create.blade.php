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
                        <h3>Neue Lösung</h3>
                        <p class="text-muted">Vereint mehrere fachlich zusammenhängende Prozesse in einer Software-Lösung.</p>
                    </div>
                </div>
                <div class="container bg-white border p-3">
                    <create-solution
                        :namespaces="{{json_encode($namespaces)}}"
                        :selected-namespace="'{{$selectedNamespace}}'"></create-solution>
                </div>
            </div>
        </div>
    </div>
@endsection

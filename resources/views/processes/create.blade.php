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
                        <h3>Neuer Prozess</h3>
                        <p class="text-muted">Repräsentiert einen fachlichen Ablauf.</p>
                    </div>
                </div>
                <div class="container bg-white border p-3">
                    <create-process
                        :namespaces="{{json_encode($namespaces)}}"
                        :processes="{{json_encode($processes)}}"
                        :licenses-processes="{{json_encode($licensesProcesses)}}"
                        :selected-namespace="'{{$selectedNamespace}}'"
                        :organisations="{{json_encode($organisations)}}"
                    >
                    </create-process>
                </div>
            </div>
        </div>
    </div>
@endsection

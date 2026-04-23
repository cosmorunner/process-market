@extends('layouts.app')

@push('header_js')
    <!-- Optionales JS hinzufügen -->
    <script src="{{ mix('js/index.js') }}" defer></script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row" style="background-image: url('/img/bg-graph.png');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;">
            <div class="col">
                <div class="container">
                    <div class="row my-5 py-5">
                        <div class="col">
                            <h1 class="text-white font-weight-bold text-center"
                                style="font-size: 3rem">{{ config('app.name') }}</h1>
                            <h5 class="text-white line-height-1-5x text-center">{{ config('app.description') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5">
                <explore-processes :items-per-page="{{$countPerPage}}" :endpoint="'{{$endpoint}}'" :tags="{{json_encode($tags)}}"
                                   :selected="{{json_encode($selectedTags)}}"></explore-processes>
            </div>
        </div>
    </div>
    <hr class="mt-5 mb-0"/>
    <div class="container-fluid bg-white py-4">
        <div class="row">
            <div class="col">
                <div class="container">
                    <div class="row my-4">
                        <div class="col text-center">
                            <h2>So funktioniert's</h2>
                            <p class="text-muted">Erstellen die hochwertige digitale Lösungen in drei Schritten.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-12 mb-4">
                            <h3 style="line-height: 2.5rem"><span class="text-primary font-weight-bold">1.</span>
                                Prozess graphisch modellieren</h3>
                            <p>Prozesse regelbasiert modellieren und Webformulare in einem Editor erstellen. </p>
                            <ul class="list-group list-group-flush bg-transparent">
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Umfangreicher Formulareditor</span>
                                </li>
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Fachlichen Prozess direkt simulieren</span>
                                </li>
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Aufbau eines eigenen Prozess-Repositories</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4 col-12 mb-4">
                            <h3 style="line-height: 2.5rem"><span class="text-primary font-weight-bold">2.</span>
                                Prozess zur Prozess-Engine übertragen</h3>
                            <p>Fertiggestellte Prozessversionen in beliebige Plattformen per integrierter Schnittstelle
                                übertragen.</p>
                            <ul class="list-group list-group-flush bg-transparent">
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Prozesse qualitätsgesichert versionieren</span>
                                </li>
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Einfaches Rollout mit integrierter Schnittstelle</span>
                                </li>
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Beliebig viele Prozess-Engines verbinden</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4 col-12 mb-4">
                            <h3 style="line-height: 2.5rem"><span class="text-primary font-weight-bold">3.</span>
                                Prozess in der Prozess-Engine ausführen</h3>
                            <p>Nach dem Export den Prozess in der Prozess-Engine regelkonform ausführen.</p>
                            <ul class="list-group list-group-flush bg-transparent">
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Echte Prozessausführung</span>
                                </li>
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>Rechte- & Rollensystem</span>
                                </li>
                                <li class="list-group-item bg-transparent px-1 py-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span>REST, SOAP, SFTP und Datenbank Schnittstellen</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="mb-5 mt-0"/>
    <div class="container mb-5">
        <div class="row my-5">
            <div class="col text-center">
                <h2>Jetzt starten</h2>
                <p class="text-muted">Privat oder in einer Organisation kollaborativ Prozesse entwickeln.</p>
                <a href="{{route('register')}}" class="btn btn-primary">Kostenlos registrieren</a>
            </div>
        </div>
    </div>

@endsection

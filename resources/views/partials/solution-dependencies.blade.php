<?php

/* @var \App\Models\SolutionVersion $solutionVersion */

?>

<div class="mt-2">
    <a data-toggle="collapse" href="#dependencies-{{$solutionVersion->id}}"
       aria-expanded="false" aria-controls="dependencies-{{$solutionVersion->id}}">
        <span class="material-icons">keyboard_arrow_down</span> Abhängigkeiten
        <span
            class="badge badge-light">{{collect($dependencies)->flatten()->count()}}</span>
    </a>
    <div class="collapse mt-2" id="dependencies-{{$solutionVersion->id}}">
        @if($dependencies['process_types'] ?? null)
            <div>
                <span>Prozesse</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['process_types'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['action_type_component_plugins'] ?? null)
            <div class="mb-2">
                <span>Aktionstyp-Plugins</span>
                <ul class="list-group">
                    @foreach($dependencies['action_type_component_plugins'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['status_type_plugins'] ?? null)
            <div class="mb-2">
                <span>Statustyp-Plugins</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['status_type_plugins'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['custom_processor_plugins'] ?? null)
            <div class="mb-2">
                <span>Prozessor-Plugins</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['custom_processor_plugins'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['group_aliases'] ?? null)
            <div class="mb-2">
                <span>Gruppen</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['group_aliases'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['user_aliases'] ?? null)
            <div class="mb-2">
                <span>Benutzer</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['user_aliases'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['bot_aliases'] ?? null)
            <div class="mb-2">
                <span>Bot</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['bot_aliases'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['connector_request_identifiers'] ?? null)
            <div class="mb-2">
                <span>Connector-Anfragen</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['connector_request_identifiers'] ?? [] as $connectorIdentifier => $requestIdentifiers)
                        @foreach($requestIdentifiers as $requestIdentifier)
                            <li class="border-0 text-muted list-group-item p-0 pl-3">{{$connectorIdentifier}}
                                / {{$requestIdentifier}}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['public_api_identifiers'] ?? null)
            <div class="mb-2">
                <span>Öffentliche APIs</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['public_api_identifiers'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($dependencies['variable_identifiers'] ?? null)
            <div class="mb-2">
                <span>Variablen</span>
                <ul class="list-group border-0">
                    @foreach($dependencies['variable_identifiers'] ?? [] as $dependency)
                        <li class="border-0 text-muted list-group-item p-0 pl-3">{{$dependency}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

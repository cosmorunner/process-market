<div class="card-header text-white d-flex justify-content-between p-2">
    <h6 class="m-0 d-flex align-items-center text-truncate" title="{{$actionType->name}}">
        <span class="text-truncate">
            <span class="d-inline-block user-context-icons">
                @if ($category?->name === 'Versteckt')
                    <span class="material-icons mr-1 mi-2x">miscellaneous_services</span>
                @else
                    <span class="material-icons mr-1 mi-2x">person</span>
                @endif
            </span>
            <span class="d-none executable-action material-icons mi-2x p-1 rounded-circle text-white" style="background-color:#339989;">play_arrow</span>
            <span class="d-none inaccessible-action material-icons mi-2x p-1 rounded-circle text-white" style="background-color:#f4c243;">lock</span>
            <span class="d-none inactive-action material-icons mi-2x p-1 rounded-circle text-white" style="background-color:#E64A69;">block</span>
            {{Str::limit($actionType->name, 22)}}
        </span>
    </h6>
    @include('cytoscape.partials.dropdown', ['nodeType' => $nodeType, 'actionType' => $actionType])
</div>
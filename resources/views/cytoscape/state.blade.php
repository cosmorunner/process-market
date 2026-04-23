<div class="cytoscape-node cytoscape-state type-{{$type}} card" id="{{$state->id}}">
    @include('cytoscape.partials.body', ['nodeType' => 'state', 'state' => $state])
    @include('cytoscape.partials.footer', ['nodeType' => 'state', 'state' => $state, 'isEnd' => $isEnd])
</div>
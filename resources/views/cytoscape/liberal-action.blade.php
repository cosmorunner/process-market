<div class="cytoscape-node cytoscape-action card" id="{{$actionType->id}}">
    @include('cytoscape.partials.header', ['nodeType' => 'liberal-action', 'actionType' => $actionType, 'category' => $category])
    @include('cytoscape.partials.body', ['nodeType' => 'liberal-action', 'roles' => $roles])
    @include('cytoscape.partials.footer', ['nodeType' => 'liberal-action', 'actionType' => $actionType])
</div>
<div class="cytoscape-node cytoscape-action card" id="{{$actionType->id}}">
    @include('cytoscape.partials.header', ['nodeType' => 'action', 'actionType' => $actionType, 'category' => $category])
    @include('cytoscape.partials.body', ['nodeType' => 'action', 'roles' => $roles])
    @include('cytoscape.partials.footer', ['nodeType' => 'action', 'actionType' => $actionType])
</div>
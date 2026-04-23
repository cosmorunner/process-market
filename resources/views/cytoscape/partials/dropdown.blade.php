<div class="dropdown dropright {{$nodeType === 'state' ? '' : 'd-flex'}}" id="contextMenu">
    <button type="button" class="btn btn-sm {{$nodeType === 'state' ? 'd-flex' : 'text-white'}} p-1"
            data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
        <span class="material-icons mi-1-25x">more_vert</span>
    </button>
    <div class="dropdown-menu">
        @if($nodeType === 'state')
            <button class="dropdown-item" type="button" onclick="cy.app.editState({
                    modelId: '{{$state->id}}',
                    statusTypeId: '{{$state->status_type_id}}'
                })">
                Bearbeiten
            </button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item text-danger" type="button" onclick="cy.app.deleteState({
                    modelId: '{{$state->id}}',
                    statusTypeId: '{{$state->status_type_id}}'
                })">
                Löschen
            </button>
        @else
            <button class="dropdown-item" type="button"
                    onclick="cy.app.editActionType({modelId: '{{$actionType->id}}'})">
                Bearbeiten
            </button>
            <button class="dropdown-item" type="button"
                    onclick="cy.app.copyActionType({modelId: '{{$actionType->id}}'})">
                Kopieren
            </button>
            @if($nodeType === 'action')
                <button class="dropdown-item" type="button"
                        onclick="cy.app.newActionRule({modelId: '{{$actionType->id}}', statusTypeId: '{{count($actionType->actionRules) > 0 ? $actionType->actionRules[0]->status_type_id : $actionType->statusRules[0]->status_type_id}}'})">
                    Neue Aktionsregel
                </button>
                <button class="dropdown-item" type="button"
                        onclick="cy.app.newStatusRule({modelId: '{{$actionType->id}}', statusTypeId: '{{count($actionType->actionRules) > 0 ? $actionType->actionRules[0]->status_type_id : $actionType->statusRules[0]->status_type_id}}'})">
                    Neue Statusregel
                </button>
            @endif
            <div class="dropdown-divider"></div>
            <button class="dropdown-item text-danger" type="button"
                    onclick="cy.app.deleteActionType({modelId: '{{$actionType->id}}'})">
                Löschen
            </button>
        @endif
    </div>
</div>
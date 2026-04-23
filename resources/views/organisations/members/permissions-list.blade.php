<div class="row">
    <div class="col-12">
        <table class="table">
            <thead>
            <tr class="d-flex">
                <th class="col-2" scope="col"></th>
                <th class="col-2" scope="col">Eigentümer</th>
                <th class="col-2" scope="col">Administrator</th>
                <th class="col-2" scope="col">Manager</th>
                <th class="col-2" scope="col">Prozess-Entwickler</th>
                <th class="col-2" scope="col">Reporter</th>
            </tr>
            </thead>
            <tbody>
            @foreach(config('roles.owner.permissions', []) as $ident)
                <tr class="d-flex">
                    <th class="col-2" scope="row">{{ $ownerPermissions->firstWhere('ident', '=', $ident)->name }}</th>
                    <td class="col-2">
                        @if($ownerPermissions->firstWhere('ident', '=', $ident))
                            <span class="material-icons text-success">done</span>
                        @endif
                    </td>
                    <td class="col-2">
                        @if($adminPermissions->firstWhere('ident', '=', $ident))
                            <span class="material-icons text-success">done</span>
                        @endif
                    </td>
                    <td class="col-2">
                        @if($managerPermissions->firstWhere('ident', '=', $ident))
                            <span class="material-icons text-success">done</span>
                        @endif
                    </td>
                    <td class="col-2">
                        @if($developerPermissions->firstWhere('ident', '=', $ident))
                            <span class="material-icons text-success">done</span>
                        @endif
                    </td>
                    <td class="col-2">
                        @if($reporterPermissions->firstWhere('ident', '=', $ident))
                            <span class="material-icons text-success">done</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
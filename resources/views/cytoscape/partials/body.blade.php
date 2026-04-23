@if($nodeType === 'state')
    <div class="card-body d-flex justify-content-between align-items-center p-2">
        <h6 class="mb-0 text-truncate" title="{{$state->description}}">{{ $state->description }}</h6>
        @include('cytoscape.partials.dropdown', ['state' => $state, 'nodeType' => 'state'])
    </div>
@else
    <div class="card-body d-flex justify-content-between align-items-center p-2">
        <div class="role-wrapper">
            @forelse($roles as $key => $role)
                @if($key < 2)
                    <span class="badge badge-light font-size-100">{{$role}}</span>
                @endif
            @empty
                <span class="material-icons text-danger">priority_high</span>
                <span>Keine Rolle hat Zugriff.</span>
            @endforelse
        </div>

        @if(count($roles) > 2)
            <div class="dropdown dropleft" id="roleDropdown">
                <button type="button" class="btn btn-sm p-0" data-toggle="dropdown" aria-expanded="false"
                        aria-haspopup="true">
                    <span class="material-icons">expand_more</span>
                </button>
                <div class="dropdown-menu">
                    @foreach($roles as $key => $role)
                        <span class="dropdown-item badge badge-light">{{$role}}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
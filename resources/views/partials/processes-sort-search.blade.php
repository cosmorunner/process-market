<div class="row mb-2">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <ul class="list-inline btn-group-sm d-flex flex-wrap align-items-center mb-0" style="row-gap: 0.5rem" id="sort">
            @php
                $currentSort = request('sort', 'lastChange_desc');
                $sortOptions = [
                    'lastChange' => 'Letzte Änderung',
                    'alphabetically' => 'Alphabetisch',
                    'complexity' => 'Komplexität'
                ];
            @endphp

            @foreach($sortOptions as $key => $label)
                @php
                    $isCurrent = str_starts_with($currentSort, $key);
                    $currentDirection = $isCurrent && !str_contains($currentSort, '_desc') ? 'asc' : 'desc';
                    $nextDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                    $icon = $currentDirection === 'asc' ? 'arrow_upward' : 'arrow_downward';
                    $nextSort = "{$key}_$nextDirection";
                    $queryParams = [
                        'sort' => $nextSort,
                        'search' => request()->query('search'),
                        'archived' => request()->query('archived')
                        ];

                @endphp
                <li class="list-inline-item">
                    <a class="btn btn-light {{ $isCurrent ? 'active' : '' }}" href="{{
                                route($routeName, array_merge($routeParams ?? [], $queryParams))
                               }}">
                        {{ $label }}
                        @if($isCurrent)
                            <span class="material-icons">{{ $icon }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
        <form method="GET" action="{{ route($routeName, array_merge($routeParams ?? [], $queryParams ?? [])) }}"
              class="d-flex align-items-center">
            @php
                $archived = request('archived', false);
            @endphp
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="archived" name="archived" value="1"
                       @if($archived) checked @endif onchange="this.form.submit()">
                <label class="custom-control-label" for="archived">Archiv</label>
            </div>
            <input type="hidden" name="sort" value="{{ request()->query('sort', 'lastChange_desc') }}">
            <input type="hidden" name="search" value="{{ request()->query('search') }}">
        </form>
        <form class="form-inline d-flex align-items-center" method="GET"
              action="{{ route($routeName, array_merge($routeParams ?? [], $queryParams ?? [])) }}">
            <div class="input-group max-width-250">
                <div class="input-group-prepend bg-light rounded-left">
                    <button class="btn btn-outline-light border" type="button" id="button-addon1"
                            onclick="this.form.submit();">
                        <span class="material-icons mi-1-25x text-primary">search</span>
                    </button>
                </div>
                <input type="text" name="search" id="process-search" placeholder="Suche..." aria-label="Suche" class="form-control"
                       value="{{ request()->query('search') }}">
                <div class="input-group-append bg-light border-left-0 rounded-right" style="z-index: 0;">
                    <button type="button" class="btn text-danger input-group-text bg-transparent"
                            aria-label="Suche löschen"
                            onclick="document.querySelector('#process-search').value=''; this.form.submit();">
                        <span class="material-icons">clear</span>
                    </button>
                </div>
            </div>
            <!-- To retain all query parameters with the exception of 'search'.
            Currently only 'sort' and 'archived', add new hidden inputs if required. -->
            <input type="hidden" name="sort" value="{{ request()->query('sort', 'lastChange_desc') }}">
            <input type="hidden" name="archived" value="{{ request()->query('archived') }}">
        </form>
    </div>
</div>
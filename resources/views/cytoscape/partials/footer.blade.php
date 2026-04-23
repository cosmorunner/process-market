@if($nodeType === 'state')
    @php
        $min = (float)$state->min;
        $max = (float)$state->max;

        // Remove unnecessary zeros after the decimal point and trim the point if no decimal places remain
        $min = $min != floor($min) ? rtrim(rtrim(number_format($min, 3, '.', ''), '0'), '.') : (int)$min;
        $max = $max != floor($max) ? rtrim(rtrim(number_format($max, 3, '.', ''), '0'), '.') : (int)$max;

        $valueRange = $min != $max ?  $min . ' - ' . $max : $min;
    @endphp

    <div class="card-footer d-flex justify-content-between align-items-center p-2">
        <span class="text-muted">{{$valueRange}}</span>
        @if($isEnd)
            <span class="material-icons">keyboard_tab</span>
        @endif
    </div>
@else
    @php
        // Mapping between identifier and associated icons and tooltips
        $processorIcons = [
            'send_email' => ['icon' => 'mail', 'tooltip' => 'E-Mailversand'],
            'create_document' => ['icon' => 'article', 'tooltip' => 'Dokumenterzeugung'],
            'delete_access' => ['icon' => 'lock', 'tooltip' => 'Zugriff entziehen'],
            'create_access' => ['icon' => 'lock_open', 'tooltip' => 'Zugriff erteilen'],
            'trigger_connector' => ['icon' => 'swap_horiz', 'tooltip' => 'Connectoraufruf'],
            'redirect' => ['icon' => 'subdirectory_arrow_right', 'tooltip' => 'Weiterleitung'],
            'execute_action' => ['icon' => 'bolt', 'tooltip' => 'Aktionsausführung'],
            'create_process' => ['icon' => 'add', 'tooltip' => 'Neuen Prozess erstellen']
        ];

        $processors = false;

        foreach ($actionType->processors as $processor) {
            if (array_key_exists($processor->identifier, $processorIcons)) {
                $processors = true;
                break;
            }
        }
    @endphp

    @if($processors)
        <div class="card-footer d-flex justify-content-between align-items-center p-2">
            <div class="d-flex flex-nowrap mr-3">
                @foreach($actionType->processors->unique('identifier') as $processor)
                    @if(array_key_exists($processor->identifier, $processorIcons) && $processor->identifier !== 'execute_action' && $processor->identifier !== 'create_process')
                        <span class="material-icons processor-icon"
                              title="{{ $processorIcons[$processor->identifier]['tooltip'] }}">
                            {{ $processorIcons[$processor->identifier]['icon'] }}
                    </span>
                    @endif
                @endforeach
            </div>

            <div class="d-flex flex-nowrap">
                @foreach($actionType->processors->unique('identifier') as $processor)
                    @if($processor->identifier === 'execute_action')
                        <div class="rounded-circle d-flex flex-wrap justify-content-center align-content-center mr-1"
                             title="{{ $processorIcons[$processor->identifier]['tooltip'] }}">
                            <span class="material-icons text-white">
                                {{ $processorIcons[$processor->identifier]['icon'] }}
                            </span>
                        </div>
                    @endif
                    @if($processor->identifier === 'create_process')
                        <div class="rounded-circle d-flex flex-wrap justify-content-center align-content-center bg-success"
                             title="{{ $processorIcons[$processor->identifier]['tooltip'] }}">
                            <span class="material-icons text-white">
                                {{ $processorIcons[$processor->identifier]['icon'] }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
@endif
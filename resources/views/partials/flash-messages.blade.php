<!-- Für API-Requests -->
<!-- Für API-Requests -->
<flash-messages
    :locals="{{json_encode(['undo' => trans_choice('app.undo', 2), 'notices' => trans_choice('app.notices', 2), 'warnings' => trans_choice('app.warnings', 2), 'info' => trans_choice('app.notices', 2), 'errors' => trans_choice('app.errors', 2)])}}"
>
</flash-messages>

@if ($message = session('success') ?? session('status') ?? ($flashSuccess ?? null) ?? ($flashStatus ?? null))
    @include('partials.flash-message', ['message' => $message, 'class' => 'success', 'header_trans' => 'app.notices'])
@endif

@if ($message = session('error') ?? ($flashError ?? null))
    @include('partials.flash-message', ['message' => $message, 'class' => 'danger', 'header_trans' => 'app.errors'])
@endif

@if ($message = session('warning') ?? ($flashWarning ?? null))
    @include('partials.flash-message', ['message' => $message, 'class' => 'warning', 'header_trans' => 'app.warnings'])
@endif

@if ($message = session('info') ?? ($flashInfo ?? null))
    @include('partials.flash-message', ['message' => $message, 'class' => 'info', 'header_trans' => 'app.notices'])
@endif

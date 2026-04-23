
@if ($errors->{ $bag ?? 'default' }->any())
    <ul class="mt-4">
        @foreach ($errors->{ $bag ?? 'default' }->all() as $error)
            <li class="text-sm text-red">{{ $error }}</li>
        @endforeach
    </ul>
@endif

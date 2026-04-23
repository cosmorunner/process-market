@foreach ($errors->{ $bag ?? 'default' }->get($key) as $error)
    <div class="invalid-feedback d-block">{{__($error)}}</div>
@endforeach

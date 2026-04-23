@extends('organisations.settings.index')

@section('settings.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                <div class="container px-0">
                    <form id="form-update-account" role="form" method="POST"
                          action="{{route('organisation.settings.update_data', $organisation)}}" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-lg-8">
                                <label for="email" class="control-label">Name</label>
                                <div>
                                    <div class="form-group">
                                        <input type="email" class="form-control {{$errors->{ $bag ?? 'default'}->has('name') ? 'is-invalid' : '' }}"
                                               id="name" name="name" placeholder="" value="{{old('name') ?? $organisation->name}}">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('name') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <label for="description" class="control-label">Beschreibung</label>
                                <div>
                                    <div class="form-group">
                                        <textarea type="text" class="form-control {{$errors->{ $bag ?? 'default'}->has('description') ? 'is-invalid' : '' }}"
                                                  id="description" name="description">{{old('description') ?? $organisation->description}}</textarea>
                                        @foreach ($errors->{ $bag ?? 'default' }->get('description') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <label for="city" class="control-label">Stadt</label>
                                <div>
                                    <div class="form-group">
                                        <input type="text" class="form-control {{$errors->{ $bag ?? 'default'}->has('city') ? 'is-invalid' : '' }}"
                                               id="city" name="city" placeholder="" value="{{old('city') ?? $organisation->city}}">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('city') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <label for="link" class="control-label">Website</label>
                                <div>
                                    <div class="form-group">
                                        <input type="text" class="form-control {{$errors->{ $bag ?? 'default'}->has('link') ? 'is-invalid' : '' }}"
                                               id="link" name="link" placeholder="" value="{{old('city') ?? $organisation->link}}">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('link') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-success">Speichern</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

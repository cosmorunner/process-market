@extends('settings.index')

@section('settings.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                <div class="container px-0">
                    <form id="form-update-account" role="form" method="POST"
                          action="{{route('settings.update_data')}}" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-lg-8">
                                <label for="email" class="control-label">E-Mail Adresse</label>
                                <div>
                                    <div class="form-group">
                                        <input type="email" class="form-control {{$errors->{ $bag ?? 'default'}->has('email') ? 'is-invalid' : '' }}"
                                               id="email" name="email" placeholder="" value="{{old('email') ?? $user->email}}">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('email') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <label for="bio" class="control-label">Bio</label>
                                <div>
                                    <div class="form-group">
                                        <textarea type="text" class="form-control {{$errors->{ $bag ?? 'default'}->has('bio') ? 'is-invalid' : '' }}"
                                                  id="bio" name="bio">{{old('bio') ?? $user->bio}}</textarea>
                                        @foreach ($errors->{ $bag ?? 'default' }->get('bio') as $error)
                                            <div class="invalid-feedback">{{$error}}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <label for="city" class="control-label">Stadt</label>
                                <div>
                                    <div class="form-group">
                                        <input type="text" class="form-control {{$errors->{ $bag ?? 'default'}->has('city') ? 'is-invalid' : '' }}"
                                               id="city" name="city" placeholder="" value="{{old('city') ?? $user->city}}">
                                        @foreach ($errors->{ $bag ?? 'default' }->get('city') as $error)
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

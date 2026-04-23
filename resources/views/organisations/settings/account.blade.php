@extends('organisations.settings.index')

@section('settings.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row mb-2">
            <div class="col">
                <h4 class="text-danger">Organisation auflösen</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <p class="text-muted mb-2"><span class="material-icons">error_outline</span> Dies kann nicht rückgängig gemacht werden.</p>
                <ul class="list-group mb-3 list-group-flush text-muted">
                    <li class="p-1 pl-2 list-group-item">Alle Prozesse der Organisation werden auf "Privat" gesetzt.</li>
                    <li class="p-1 pl-2 list-group-item">Die Organisation ist nicht mehr auffindbar.</li>
                    <li class="p-1 pl-2 list-group-item">Alle Mitglieder werden entfernt.</li>
                </ul>
            </div>
            <div class="col-sm-12 col-md-8 d-flex align-items-stretch">
                <div class="container px-0 d-flex align-items-stretch">
                    <form class="w-100 d-flex flex-column justify-content-between" role="form" method="POST"
                          action="{{route('organisation.delete', $organisation)}}" novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="col-lg-8">
                            <label for="accept" class="control-label">&nbsp;</label>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="accept" class="custom-control-input" id="accept">
                                    <label class="custom-control-label" for="accept">Sind Sie sicher?</label>
                                </div>
                                @foreach ($errors->{ $bag ?? 'default' }->get('accept') as $error)
                                    <div class="invalid-feedback d-block">{{$error}}</div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-danger">Unwiderruflich auflösen</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

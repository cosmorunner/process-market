<?php
/* @var \App\Models\Process $process */
?>

@extends('processes.edit')

@section('processes.edit.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <h4 class="text-danger">Prozess löschen</h4>
                <p class="text-muted mb-2"><span class="material-icons">error_outline</span> Dies kann nicht rückgängig
                    gemacht werden.</p>
            </div>
            <div class="col-sm-12 col-md-8">
                <div class="container px-0">
                    <form id="form-update-password" role="form" method="POST"
                          action="{{route('process.destroy', ['process' => $process])}}"
                          novalidate>
                        @csrf
                        @method('DELETE')
                        <div class="row">
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
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-danger">Unwiderruflich löschen</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

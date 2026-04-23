<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\User $user */

?>

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row pt-4 mb-3">
            <div class="col">
                <a href="{{route('organisation.members', $organisation)}}">Zurück zur Mitgliederübersicht</a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h3>Einladen</h3>
            </div>
        </div>
    </div>
    <form id="form-update-password" class="w-100 d-flex flex-column justify-content-between" role="form" method="POST"
          action="{{route('organisation.invitation.store', $organisation)}}" novalidate>
        @csrf
        <div class="container bg-white border p-3">
            <div class="row mb-2">
                <div class="col">
                    <h4>E-Mail</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <p class="text-muted mb-2">Die Einladung wird an die E-Mail Adresse gesendet und ist eine Woche
                        gültig.</p>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="container px-0">
                        <div class="row">
                            <div class="col-lg-8">
                                <label for="email" class="control-label">E-Mail</label>
                                <div class="form-group">
                                    <input type="email" value="{{old('email') ?? ''}}" name="email"
                                           class="form-control {{$errors->{ $bag ?? 'default'}->has('email') ? 'is-invalid' : '' }}"/>
                                    <small class="text-muted">Die Person erhält eine E-Mail von "ZenByte
                                        Solutions".</small>
                                    @foreach ($errors->{ $bag ?? 'default' }->get('email') as $error)
                                        <div class="invalid-feedback">{{$error}}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <h4>Mitgliedschaft</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4"></div>
                <div class="col-sm-12 col-md-8 d-flex align-items-stretch">
                    <div class="container px-0 d-flex justify-content-between flex-column">
                        <div class="row">
                            <div class="col-lg-8">
                                <label for="role" class="control-label">Rolle</label>
                                <div class="form-group">
                                    <select name="role"
                                            class="form-control {{$errors->{ $bag ?? 'default'}->has('role') ? 'is-invalid' : '' }}"
                                            required>
                                        <option value="">Bitte wählen...</option>
                                        @foreach($organisation->roles as $role)
                                            @if($role->name !== 'Eigentümer')
                                                <option value="{{$role->id}}">{{$role->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @foreach ($errors->{ $bag ?? 'default' }->get('role') as $error)
                                        <div class="invalid-feedback">{{$error}}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm btn-success">Einladen</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <h4>Rollen-Rechte</h4>
                </div>
            </div>
            @include('organisations.members.permissions-list')
        </div>
    </form>
@endsection

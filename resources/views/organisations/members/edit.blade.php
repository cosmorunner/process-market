<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\User $user */
/* @var \App\Models\Role $userRole */

/* @var \Illuminate\Support\Collection $founderPermissions */
/* @var \Illuminate\Support\Collection $adminPermissions */
/* @var \Illuminate\Support\Collection $managerPermissions */
/* @var \Illuminate\Support\Collection $developerPermissions */
/* @var \Illuminate\Support\Collection $reporterPermissions */

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
                <h3>{{$user->namespace}} bearbeiten</h3>
            </div>
        </div>
    </div>
    <div class="container bg-white border p-3">
        <div class="row mb-3">
            <div class="col">
                <h4>Mitgliedschaft</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form role="form" method="POST"
                      action="{{route('organisation.members.update', ['organisation' => $organisation, 'user' => $user])}}"
                      novalidate>
                    <label for="role" class="control-label">Rolle</label>
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <select name="role"
                                class="form-control {{$errors->{ $bag ?? 'default'}->has('role') ? 'is-invalid' : '' }}">
                            @foreach($organisation->roles as $role)
                                @if($userRole->isOwner() || $role->name !== 'Eigentümer')
                                    <option value="{{$role->id}}" {{$userRole->id === $role->id ? 'selected' : ''}}>{{$role->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        @foreach ($errors->{ $bag ?? 'default' }->get('role') as $error)
                            <div class="invalid-feedback d-block">{{$error}}</div>
                        @endforeach
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-sm btn-success">Ändern</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h4>Rollen-Rechte</h4>
            </div>
        </div>
        @include('organisations.members.permissions-list')
        @if($organisation->roleOf(\Illuminate\Support\Facades\Auth::user())->isOwner() && !$organisation->roleOf($user)->isOwner())
            <hr class="my-4"/>
            <div class="row mb-2">
                <div class="col">
                    <h4 class="text-warning">Eigentümer-Role übertragen</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <p class="text-muted">Der Benutzer "{{$user->namespace}}", wird anstelle von Ihnen, zum Eigentümner dieser Organisation.</p>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="container px-0">
                        <form id="form-update-onwership" role="form" method="POST"
                              action="{{route('organisation.members.transfer-owner', ['organisation' => $organisation, 'user' => $user])}}"
                              novalidate>
                            @csrf
                            @method('patch')
                            <div class="row">
                                <div class="col-lg-8">
                                    <label for="accept" class="control-label">&nbsp;</label>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="accept" class="custom-control-input"
                                                   id="accept2">
                                            <label class="custom-control-label" for="accept2"></label>
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
                                        <button type="submit" class="btn btn-sm btn-warning">Zum Eigentümer machen</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->id !== $user->id)
            <hr class="my-4"/>
            <div class="row mb-2">
                <div class="col">
                    <h4 class="text-danger">Entfernen</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <p class="text-muted">Dem Benutzer "{{$user->namespace}}" wird der Zugriff auf die Organisation
                        entzogen.</p>
                </div>
                <div class="col-sm-12 col-md-8">
                    <div class="container px-0">
                        <form id="form-update-password" role="form" method="POST"
                              action="{{route('organisation.members.delete', ['organisation' => $organisation, 'user' => $user])}}"
                              novalidate>
                            @csrf
                            @method('DELETE')
                            <div class="row">
                                <div class="col-lg-8">
                                    <label for="accept" class="control-label">&nbsp;</label>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="accept" class="custom-control-input"
                                                   id="accept">
                                            <label class="custom-control-label" for="accept"></label>
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
                                        <button type="submit" class="btn btn-sm btn-danger">Entfernen</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

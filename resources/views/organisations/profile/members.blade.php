<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\Access[] $accesses */

?>

@extends('organisations.profile.index')

@section('profile.content')
    <div class="container bg-white border border-top-0 p-3">
        @can('manageMembers', $organisation)
            <div class="row mb-3">
                <div class="col">
                    <a href="{{route('organisation.members.invite', $organisation)}}">
                        <button class="btn btn-sm btn-outline-primary">
                            <span class="material-icons">email</span> Einladen
                        </button>
                    </a>
                </div>
            </div>
        @endcan
        <div class="row">
            <div class="col">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col" class="w-25">Benutzer</th>
                        <th scope="col" class="w-25">Rolle</th>
                        <th scope="col" class="w-15">Mitglied seit</th>
                        <th scope="col" class="w-20"></th>
                        <th scope="col" class="w-15"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($accesses->sortBy('recipient.namespace') as $access)
                        <tr>
                            <td>
                                <a href="{{route('user.show', ['user' => $access->recipient])}}">{{$access->recipient->namespace}}</a>
                            </td>
                            <td>{{ $access->role->name }}</td>
                            <td>{{ $access->created_at->format('d.m.Y') }}</td>
                            <td>
                                @if($access->recipient_id === $user->id)
                                    <span class="badge badge-primary">Hier bist du!</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @can('manageMembers', $organisation)
                                    @if(!$access->role->isAdmin() || $userRole->isOwner() || $access->recipient_id === auth()->user()->id)
                                        <a href="{{ route('organisation.members.edit', ['organisation' => $organisation, 'user' => $access->recipient]) }}">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <span class="material-icons">settings</span>
                                            </button>
                                        </a>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    </tbody>
                    @empty
                    @endforelse
                </table>
            </div>
        </div>
        @if($organisation->invitations->isNotEmpty())
            <div class="row">
                <div class="col">
                    <h5>Offene Einladungen</h5>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col" class="w-25">E-Mail</th>
                            <th scope="col" class="w-25">Rolle</th>
                            <th scope="col" class="w-25">Läuft ab</th>
                            <th scope="col" class="w-25"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($organisation->invitations as $invitation)
                            <tr>
                                <td>{{ $invitation->email }}</td>
                                <td>{{ $invitation->role->name }}</td>
                                <td>{{ $invitation->expires_at->diffForHumans() }}</td>
                                <td class="text-right">
                                    <a href="{{route('organisation.invitation.resend', ['organisation' => $organisation, 'invitation' => $invitation])}}"
                                       class="d-inline-block">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <span class="material-icons">refresh</span>
                                        </button>
                                    </a>
                                    <form method="POST" class="d-inline-block"
                                          action="{{route('organisation.invitation.delete', ['organisation' => $organisation, 'invitation' => $invitation])}}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <span class="material-icons">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                        @empty
                        @endforelse
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

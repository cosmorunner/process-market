<?php

/* @var \App\Models\Invitation $invitation */
/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\Role $role */

?>

@extends ('emails.layout')

@section('content')
    <p style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin: 0 0 15px 0;">Hallo!</p>
    <p style="font-family: sans-serif; font-size: 14px; margin: 0 0 15px 0;">Sie wurden von dem Benutzer "{{$invitation->creator->namespace}}" eingeladen, der Organisation "{{$organisation->name}}" als {{$invitation->role->name}}
        in der Prozessfabrik ({{$domain}}) beizutreten.
        <br>Klicken Sie auf den folgenden Link um die Einladung anzunehmen:<br><br>
        <a href="{{$url}}">{{$url}}</a>
    </p>
@endsection

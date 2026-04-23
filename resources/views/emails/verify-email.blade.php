<?php

/* @var \App\Models\Organisation $organisation */
/* @var \App\Models\Role $role */

?>

@extends ('emails.layout')

@section('content')
    <p style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin: 0 0 15px 0;">Hallo!</p>
    <p style="font-family: sans-serif; font-size: 14px; margin: 0 0 15px 0;">Bitte bestätigten Sie ihre Registrierung für die Prozessfabrik indem Sie auf den Link klicken:
        <br><br>
        <a href="{{$url}}">{{$url}}</a>
    </p>
@endsection

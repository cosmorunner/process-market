@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center py-4 mt-3">
            <div class="col-md-8">
                <div class="card p-4">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{$section === 'imprint' ? 'active bg-white' : ''}}" href="{{route('legal', ['section' => 'imprint'])}}">Impressum</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$section === 'terms' ? 'active bg-white' : ''}}" href="{{route('legal', ['section' => 'terms'])}}">Allgemeine Geschäftsbedingungen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$section === 'privacy' ? 'active bg-white' : ''}}" href="{{route('legal', ['section' => 'privacy'])}}">Datenschutzerklärung</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$section === 'licenses' ? 'active bg-white' : ''}}" href="{{route('legal', ['section' => 'licenses'])}}">Lizenzen</a>
                        </li>
                    </ul>
                    <div class="mt-2 py-2">
                        @include('legal.' . $section)
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

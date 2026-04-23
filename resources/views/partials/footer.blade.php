<footer>
    <div class="container-fluid bg-transparent border-top">
        <div class="row py-1 justify-content-end">
            <div class="col-md-4 col-12 d-flex justify-content-end" style="font-size: 9pt;">
                <small><a class="text-muted mr-2" href="{{route('legal', ['section' => 'imprint'])}}">Impressum</a></small>
                <small><a class="text-muted mr-2" href="{{route('legal', ['section' => 'terms'])}}">AGB</a></small>
                <small><a class="text-muted" href="{{route('legal', ['section' => 'privacy'])}}">Datenschutzerklärung</a></small>
                @auth
                    <small><a class="text-muted ml-2" href="{{route('changelog')}}">Changelog</a></small>
                @endauth
            </div>
        </div>
    </div>
</footer>

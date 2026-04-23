@foreach($runningDemos as $runningDemo)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info d-flex p-2 justify-content-between" role="alert">
                <div>
                    <span class="material-icons text-danger">circle</span>
                    <span>Lösung-Demo: {{$runningDemo->solution->name . ' - ' . $runningDemo->solutionVersion->full_namespace}}</span>
                </div>
                <div class="d-flex">
                    <a href="{{route('demo.show', ['demo' => $runningDemo]) . '?ref=' . base64_encode('profile.solutions')}}">
                        <button class="btn btn-sm btn-link py-0">
                            <span class="material-icons text-primary">play_arrow</span>
                            <span>Zur Demo</span>
                        </button>
                    </a>
                    <form method="POST" class="d-flex" action="{{route('demo.delete', ['demo' => $runningDemo])}}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ref" value="{{request('ref')}}"/>
                        <button type="submit" class="btn btn-sm btn-link py-0">
                            <span class="material-icons">stop</span>
                            <span>Beenden</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<?php
/* @var \App\Models\Simulation $runningSimulation */
/* @var \App\Models\Simulation $runningSimulation */
?>

@foreach($runningSimulations as $runningSimulation)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info d-flex p-2 justify-content-between" role="alert">
                <div>
                    <span class="material-icons text-danger">circle</span>
                    <span>Prozess-Demo: {{$runningSimulation->process->title . ' - ' . $runningSimulation->processVersion->full_namespace}}</span>
                </div>
                <div class="d-flex">
                    <a href="{{$runningSimulation->process->devPath($runningSimulation->processVersion->version)}}">
                        <button class="btn btn-sm btn-link py-0">
                            <span class="material-icons text-primary">play_arrow</span>
                            <span>Regeln & Daten</span>
                        </button>
                    </a>
                    <a href="{{route('simulation.show', ['simulation' => $runningSimulation]) . '?ref=' . base64_encode('profile.processes')}}">
                        <button class="btn btn-sm btn-link py-0">
                            <span class="material-icons text-primary">play_arrow</span>
                            <span>Allisa Plattform</span>
                        </button>
                    </a>
                    @if($runningSimulation->user_id === auth()->user()->id)
                        <form method="POST" class="d-flex" action="{{route('simulation.delete', ['simulation' => $runningSimulation])}}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="ref" value="{{request('ref')}}"/>
                            <button type="submit" class="btn btn-sm btn-link py-0">
                                <span class="material-icons">stop</span>
                                <span>Beenden</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

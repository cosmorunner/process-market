<?php
/**
 * @var App\Models\Process $process
 * @var App\Models\ProcessVersion $processVersion
 * @var App\Models\SolutionVersion $solutionVersion
 */
?>

@extends('processes.detail')

@section('processes.detail.content')
    <div class="container bg-white border border-top-0 p-3">
        <div class="row">
            <div class="col">
                <table class="table table-sm m-0">
                    <tbody>
                    @foreach($process->publishedVersions() as $processVersion)
                        <tr class="d-flex">
                            <td class="p-2 col-6 @if ($loop->first) border-0 @endif">
                                <span class="mb-2 d-inline-block">{{$processVersion->version}}</span>
                                @if($processVersion->id == $process->latest_published_version_id)
                                    <span class="badge badge-primary">latest</span>
                                @endif
                                <div class="py-2">
                                    <span class="mb-0 text-muted"
                                          style="white-space: pre-line">{{$processVersion->changelog}}</span>
                                </div>
                            </td>
                            <td class="p-2 col-2 @if($loop->first) border-0 @endif">
                                @include('partials.complexity-score', ['score' => $processVersion->complexity_score])
                            </td>
                            <td class="p-2 col-4 d-flex justify-content-end @if ($loop->first) border-0 @endif">
                                <div class="d-flex flex-column justify-content-between">
                                    @if (!$process->hasNoLicense())
                                        <a class="btn btn-primary px-2 py-0"
                                           href="{{route('process.detail.demo', ['namespace' => $process->namespace, 'identifier' => $process->identifier, 'version' => $processVersion->version]) . '?ref=' . base64_encode(url()->current())}}">
                                            <span>Demo</span>
                                        </a>
                                        <small class="text-muted text-right">{{ $processVersion->updated_at->diffForHumans() }}</small>
                                    @else
                                        <small class="text-muted text-right">
                                            Demo nicht verfügbar
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<?php

use App\Models\Access;

?>
<!-- Modal -->
<div class="modal fade" id="switchUserContext" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Organisation wählen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="organisation-list">
                    <div class="organisation-item d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5 class="mb-0">Eigenes Profil</h5>
                        </div>
                        <form action="{{ route('update_user_context') }}" class="m-0" method="post">
                            @method('PATCH')
                            @csrf
                            <input type="hidden" name="context" value="{{null}}">
                            @if(is_null($user->context))
                                <button type="button" class="btn btn-primary ml-3" disabled>Aktueller Kontext
                                </button>
                            @else
                                <button type="submit" class="btn btn-success ml-3">Wechseln</button>
                            @endif
                        </form>
                    </div>
                    <hr class="my-4">
                    @php
                        // Sortiere die Organisationen nach dem aktuellen Kontext
                        $contextAccess = $user->organisationAccesses->filter(fn(Access $access) => $access->resource_id === $user->context)->first();
                        $otherOrganisationAccesses = $user->organisationAccesses->filter(fn(Access $access) => $access->resource_id != $user->context)->sortBy('resource.name');
                    @endphp
                    @if($contextAccess)
                        <div class="organisation-item d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-0">{{ $contextAccess->resource->name}}</h5>
                                <small class="text-muted">{{ $contextAccess->role->name }}</small>
                            </div>
                            <button type="button" class="btn btn-primary ml-3" disabled>Aktueller Kontext</button>
                        </div>
                        <hr class="my-2">
                    @endif
                    @foreach($otherOrganisationAccesses as $organisationAccess)
                        <div class="organisation-item d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-0">{{ $organisationAccess->resource->name}}</h5>
                                <small class="text-muted">{{ $organisationAccess->role->name }}</small>
                            </div>
                            <form action="{{ route('update_user_context') }}" class="m-0" method="post">
                                @method('PATCH')
                                @csrf
                                <input type="hidden" name="context" value="{{ $organisationAccess->resource_id }}">
                                @if($user->context === $organisationAccess->resource_id)
                                    <button type="button" class="btn btn-primary ml-3" disabled>Aktueller
                                        Kontext
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success ml-3">Wechseln</button>
                                @endif
                            </form>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
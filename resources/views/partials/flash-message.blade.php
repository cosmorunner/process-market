@if($message instanceof Illuminate\Support\Collection && $message->unique()->count() > 0)
    <div class="alert alert-{{$class}} alert-block mb-0 px-3 py-2">
        <button type="button" class="close" data-dismiss="alert">×</button>
        @if($message->unique()->count() === 1)
            <strong>{{ (string) $message->first() }}</strong>
        @else
            @include('partials.flash-message-accordion', ['messages' => $message->unique()])
        @endif
    </div>
@elseif(is_string($message))
    <div class="alert alert-{{$class}} alert-block mb-0 px-3 py-2">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
@elseif($message instanceof \App\FlashMessages\MessageWithLinkButton)
    <div class="alert alert-{{$class}} alert-block mb-0 px-3 py-2">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message->message }}</strong>
        <a href="{{$message->route}}" class="ml-2">
            <button class="btn btn-sm btn-{{$class}}">{{$message->buttonLabel}}</button>
        </a>
    </div>
@elseif($message instanceof \App\FlashMessages\MessageWithActionUndoButton)
    <div class="alert alert-success alert-block px-3 py-2">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message->message }}</strong>
        <form action="{{$message->route}}" method="POST" class="form-inline d-inline">
            @csrf
            @method('delete')
            <input type="hidden" value="{{$message->actionId}}" name="action_id"/>
            <button class="btn btn-sm btn-{{$class}}">{{$message->buttonLabel}}</button>
        </form>
    </div>
@endif

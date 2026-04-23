<div id="{{$class}}-accordion">
    <div class="card alert-{{$class}} border-0">
        <div class="card-header alert-{{$class}} p-0 border-0" id="{{$class}}-flash-heading">
            <span class="mb-0">
                <button class="btn p-0 border-0 alert-{{$class}}" data-toggle="collapse"
                        data-target="#{{$class}}-flash-accordion" aria-expanded="true"
                        aria-controls="collapseOne">
                    <strong
                        class="dropdown-toggle">{{$message->count()}} {{trans_choice($header_trans, $message->count())}}</strong>
                </button>
            </span>
        </div>
        <div id="{{$class}}-flash-accordion" class="collapse hide" aria-labelledby="{{$class}}-flash-heading"
             data-parent="#{{$class}}-accordion">
            <div class="card-body p-2">
                <ul class="pl-2 m-0">
                    @foreach($messages as $message)
                        <li>{{$message}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

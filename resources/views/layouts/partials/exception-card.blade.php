<div class="card">
    <div class="card-body">
        <h5 class="card-title"><span class="material-icons mi-2x">sentiment_dissatisfied</span> {{$exceptionTitle}}</h5>
        @if(isset($message) && $message)
            <p class="card-text">{{$message}}</p>
        @endif
        @if(isset($backUrl) && $backUrl)
            <a href="{{$backUrl}}" class="card-link">{{__('app.back')}}</a>
        @endif
        @if(isset($enableHomePageLink) && $enableHomePageLink)
            <a href="{{route('index')}}" class="card-link">{{__('app.home_page')}}</a>
        @endif
    </div>
</div>

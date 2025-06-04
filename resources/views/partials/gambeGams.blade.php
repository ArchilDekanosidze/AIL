@foreach($gams as $gam)
    <a class="pa-book-info-item sp-flex-column" 
       href="{{$gam->url}}" 
       title="{{ $gam->title }}">
       
        <div class="book-info-item-top sp-flex-1">
            <div class="book-info-item-descimg sp-overflow-hidden">
                <img src="{{ $gam->url}}" 
                     alt="{{ $gam->title }}" 
                     class="sp-w sp-h">
            </div>

            <div class="sp-flex-1 sp-d-flex sp-flex-column sp-justify-center sp-mr-4 sp-m-auto sp-ml-1">
                <div class="book-info-item-text sp-mb-3">
                    <h2 class="sp-font-large-new2">{{ $gam->title }}</h2>
                </div>

                <div class="book-info-item-text-desc">
                    <p>{{ $gam->description }}</p>
                </div>
            </div>
        </div>
    </a>
@endforeach

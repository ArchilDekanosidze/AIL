@foreach($jozves as $jozve)
    <div class="col-md-4 col-sm-6 col-xs-12 text-center jozve-file" style="margin-bottom: 20px;">
        <div class="block" style="border: 1px solid #ddd; padding: 15px; background: #fff; box-shadow: 0 1px 7px -3px rgba(0,0,0,0.1); transition: all 0.3s;">
            <a href="{{ route('jozve.download', $jozve->id) }}" style="text-decoration: none; color: inherit;">
                <h3 style="font-size: 18px; margin-bottom: 10px;">{{ $jozve->title }}</h3>
                
                @if($jozve->description)
                    <p style="font-size: 14px; color: #555;">{{ Str::limit($jozve->description, 120) }}</p>
                @endif
        دانلود جزوه            
            </a>
        </div>
    </div>
@endforeach

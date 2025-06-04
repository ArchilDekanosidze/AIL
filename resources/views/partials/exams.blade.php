@foreach($exams as $exam)
    <div class="text-center examfile" state="0" answer="1">
        <div class="block">
            <a href="{{ url($exam->url) }}" class="downloadfile" rel="nofollow">
                <div class="examTitle" style="height:48px;">
                    {{ $exam->title }}
                </div>
                <div class="examTags">
                    {{-- Example tag rendering --}}
                    @foreach($exam->tags as $tag)
                        @php
                            $labelClass = match ($tag->type) {
                                'download' => 'label-default',
                                'month' => 'label-info rtl',
                                'answer' => 'label-success',
                                'scope' => 'label-primary',
                                default => 'label-default',
                            };
                        @endphp
                        <span class="label {{ $labelClass }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </a>
        </div>
    </div>
@endforeach

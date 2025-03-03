@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/index.css')}}">
@endsection
@section('content')
<p class="breadcrump"> @php echo $path @endphp </p>
<div class="adminCategory main-body">
    <div class="mainDivDirection">
        {{$i = 1;}}
        @foreach($questions as $question)
            <div class="questioCard">
                <div>سوال شماره : {{$i++}}</div>
                <div class="questionFront">{!! $question->front !!}</div>
                <div class="questionFront">{!! $question->p1 !!}</div>
                <div class="questionFront">{!! $question->p2 !!}</div>
                <div class="questionFront">{!! $question->p3 !!}</div>
                <div class="questionFront">{!! $question->p4 !!}</div>
                <div class="questionFront">{!! $question->back !!}</div>
                <div class="cardButtons">
                    @if($question->type == 'test')                    
                        <a class="editLink" href="{{route('admin.question.edit', $question->id)}}">ادیت</a>                    
                    @else                    
                        <a class="editLink" href="{{route('admin.question.descriptive.edit', $question->id)}}">ادیت</a>
                    @endif
                    <a class="deleteLink" href="{{route('admin.question.delete', $question->id)}}">حذف</a>                        
                    <a class="showLink" href="{{route('admin.question.show', $question->id)}}">نمایش</a>                        
                </div> 
                  
            </div>         
        @endforeach                
    </div>
</div>
@endsection
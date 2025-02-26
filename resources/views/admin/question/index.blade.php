@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/index.css')}}">
@endsection
@section('content')
<p class="breadcrump"> @php echo $path @endphp </p>
<div class="adminCategory main-body">
    <div class="mainDivDirection">
        @foreach($questions as $question)
            <div class="questioCard">
                <div class="questionFront">{!! $question->front !!}</div>
                <div class="cardButtons">
                    @if($question->type == 'test')                    
                        <a class="editLink" href="{{route('admin.question.edit', $question->id)}}">ادیت</a>                    
                    @else                    
                        <a class="editLink" href="{{route('admin.question.descriptive.edit', $question->id)}}">ادیت</a>
                    @endif
                    <a class="deleteLink" href="{{route('admin.question.delete', $question->id)}}">حذف</a>                        
                </div>    
            </div>         
        @endforeach                
    </div>
</div>
@endsection
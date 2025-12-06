@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/show.css')}}">

@endsection
@section('content')
<div class="adminCategory main-body">
    <div class="mainDivDirection">
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
        </div>                  
    </div>
    @include('partials.questionComments')
</div>


<form action="{{ route('admin.question.deActiveQuestion', $question->id) }}" method="GET">


    <button type="submit" class="deActiveQuestion">غیر فعال کردن سوال</button>
</form>
@endsection
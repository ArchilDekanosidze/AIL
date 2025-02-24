@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/index.css')}}">
@endsection
@section('content')
<p class="breadcrump"> @php echo $path @endphp </p>
<div class="adminCategory main-body">
    <div class="mainDivDirection">
        @foreach($questions as $question)
            <p class="questionFront">{{$question->front}}</p>
            <div class="cardButtons">
                <a class="editLink" href="{{route('admin.question.edit', $question->id)}}">ادیت</a>
                <a class="deleteLink" href="{{route('admin.question.delete', $question->id)}}">حذف</a>
            </div>             
        @endforeach                
    </div>
</div>
@endsection
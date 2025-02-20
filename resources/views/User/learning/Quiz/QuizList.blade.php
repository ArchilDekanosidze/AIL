@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/learning/Quiz/QuizList.css')}}">
@endsection
@section('content')
<div class="QuizResult main-body">
  
    <table class="quizTable">
        <tr>
            <th>نام آزمون</th>
            <th>تاریخ ساخته شدن</th>
            <th>وضعیت آزمون</th>
            <th>درصد آزمون</th>
            <th>رفتن به آزمون</th>
            <th>مشاهده نتیجه آزمون</th>
        </tr>

        @foreach ($quizzes as $quiz)
            <tr>
                <td>{{$quiz->quiz_name}}</td>
                <td>{{$quiz->createdAt}}</td>
                <td>{{$quiz->persian_status}}</td>
                <td>{{$quiz->finalPercentage}}</td>
                <td><a href="{{route('user.learning.onlineQuizInProgress', $quiz->id)}}">رفتن</a></td>
                <td><a class="@if($quiz->status != "ended") disabled @endif" href="{{route('learning.saveQuizDataAndShowResult', $quiz->id)}}">مشاهده</a></td>
            </tr>
        @endforeach
    </table>

</div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
        
       
           


            



        });
    </script>
@endsection
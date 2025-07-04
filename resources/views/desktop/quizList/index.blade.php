@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/desktop/quizList/quizList.css')}}">
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
                @php
                    $isOwner = $quiz->user_id === auth()->id();
                    $quizEnded = $quiz->status == 'ended';
                @endphp

                <td>
                    @if ($isOwner)
                        <a href="{{ route('quiz.online.onlineQuizInProgress', $quiz->id) }}">رفتن</a>
                    @elseif ($isSupervisor && $quizEnded)
                        <a href="{{ route('quiz.online.onlineQuizInProgress', $quiz->id) }}">رفتن</a>
                    @else
                        <span class="text-muted">غیرفعال</span>
                    @endif
                </td>

                <td><a class="@if($quiz->status != "ended") disabled @endif" href="{{route('quiz.online.saveOnlineQuizDataAndShowResult', $quiz->id)}}">مشاهده</a></td>
            </tr>
        @endforeach
    </table>
    <div class="quizTableSmallerScreenWith">
        @foreach ($quizzes as $quiz)
            <div class="quizDetail">
                <p>نام آزمون: {{$quiz->quiz_name}}</p>
                <p>تاریخ ساخته شدن: {{$quiz->createdAt}}</p>
                <p>وضعیت آزمون : {{$quiz->persian_status}}</p>
                <p>درصد آزمون : {{$quiz->finalPercentage}}</p>
                <p><a href="{{route('quiz.online.onlineQuizInProgress', $quiz->id)}}">رفتن به آزمون</a></p>
                <p><a class="@if($quiz->status != "ended") disabled @endif" href="{{route('quiz.online.saveOnlineQuizDataAndShowResult', $quiz->id)}}">مشاهده</a></p>
            </div>    
        @endforeach
    </div>

</div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
        
       
           


            



        });
    </script>
@endsection
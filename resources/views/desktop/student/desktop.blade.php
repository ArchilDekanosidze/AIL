@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/desktop/student/desktop.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('quiz.chooseCategories.student')}}">ساخت آزمون جدید</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('desktop.quizList', $userId)}}">لیست آزمون های من</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('desktop.myProgress', $userId)}}">مشاهده پیشرفته من </a>
    </div>

</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/profile/student/profile.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('quiz.chooseCategories.student')}}">ساخت آزمون جدید</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('desktop.student.quizList')}}">لیست آزمون های من</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('desktop.student.myProgress')}}">مشاهده پیشرفته من </a>
    </div>

</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
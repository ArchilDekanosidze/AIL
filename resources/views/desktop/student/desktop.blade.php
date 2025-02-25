@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/desktop/setting/setting.css')}}">
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

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('desktop.setting.setting')}}">تنظیمات امنیتی </a>
    </div>

</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
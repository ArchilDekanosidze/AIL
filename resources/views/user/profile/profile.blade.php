@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/profile/profile.css')}}">
@endsection
@section('content')
<div class="userProfile main-body">
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('user.profile.new.chooseCategoryForLearning')}}">ساخت آزمون جدید</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('user.profile.quizList')}}">لیست آزمون های من</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('user.profile.myProgress')}}">مشاهده پیشرفته من </a>
    </div>

</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
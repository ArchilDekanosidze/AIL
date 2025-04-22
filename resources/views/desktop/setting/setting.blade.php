@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/desktop/student/desktop.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('auth.otp.desktop.setting.two.factor.toggle.form')}}">تنظیمات احراز هویت دو مرحله ای</a>
    </div>
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('auth.otp.desktop.setting.mobile.form')}}">تغییر شماره موبایل</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('auth.otp.desktop.setting.email')}}">تغییر ایمیل</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('auth.desktop.setting.changeNameForm')}}">تغییر نام کاربری</a>
    </div>

    @if(!auth()->user()->hasVerifiedEmail() && auth()->user()->hasEmail())
        <div class="profileCard">        
            <a class="btn btn-primary" href="{{route('auth.email.send.verification')}}">ارسال ایمیل تاییده</a>
        </div>
    @endif


</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
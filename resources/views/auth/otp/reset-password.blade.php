@extends('layouts.master')

@section('head-tag')
<title>بازیابی رمز عبور با OTP</title>
@endsection

@section('content')

<div class="row justify-content-center mt-lg-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                        بازیابی رمز عبور به کمک OTP
                        <div class="col-sm-5 text-right"><a href="{{route('auth.password.forget.form')}}"><small>بازیابی رمز عبور به کمک لینک ایمیل</small></a></div>
            </div>
            <div class="card-body">
            <form method="POST" action="{{route('auth.otp.password.send.token')}}">
                    @csrf
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="email">@lang('public.username')</label>
                        <div class="col-sm-9">
                            <input  name="username" class="form-control" id="username" value="{{old('username')}}"
                                 placeholder="@lang('public.enter your email or phone number')">
                        </div>
                    </div>
                    <div class="col-sm-9 offset-sm-3">
                        @error('Credentials')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
                    </div>
                    <div class="offset-sm-3">
                    <button type="submit" class="btn btn-primary">@lang('public.send OTP Code')</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

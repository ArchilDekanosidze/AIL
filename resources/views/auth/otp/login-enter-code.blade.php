@extends('layouts.master')


@section('head-tag')
<title>کد را وارد کنید</title>
@endsection

@section('content')




<div class="row justify-content-center mt-lg-5  mb-lg-5">
    <div class="col-md-6">
        <div class="card">
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
            <div class="card-header">
                OTP Code
                <div class="col-sm-5 text-right"><a href="{{route('auth.otp.login.form')}}"><small>ورود با OTP</small></a></div>
                <div class="col-sm-5 text-right"><a href="{{route('auth.login.form')}}"><small>ورود با پسورد</small></a></div>
            </div>
            <div class="card-body">
                <p class="small text-center card-text">کد را برای شما ارسال کردیم</p>
            <form method="POST" action="{{route('auth.otp.login.code')}}">
                        @csrf
                        <div class="form-group row mb-lg-2">
                            <div class="col-sm-8 offset-sm-2">
                                <input type="text" name="code" class="form-control" id="code"
                                    aria-describedby="codeHelp" placeholder="@lang('public.enter code')">
                            </div>
                        </div>
                        <div class="col-sm-9 offset-sm-3 mb-lg-2">
                        </div>
                        <div class="offset-sm-3">
                            <button type="submit" class="btn btn-primary">@lang('public.confirm')</button>
                        <a class="small ml-2" href="{{route('auth.otp.login.resend')}}">@lang('public.didNotGetCode')</a>
                        </div>
                        @error('code')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                   code is not valid
                                </strong>
                            </span>
                        @enderror
                        @error('cantSendCode')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
                        @error('invalidCode')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@extends('layouts.master')
@section('head-tag')
<title>فراموشی رمز عبور</title>
@endsection
@section('content')


<div class="row justify-content-center mt-lg-5">
    <div class="col-md-6">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <div class="card">
            <div class="card-header" style="display: flex;justify-content: space-between;">
               فراموشی رمز عبور
                <a href="{{route('auth.otp.password.forget.form')}}">بازیابی رمز عبور با OTP</a>
            </div>
            <div class="card-body">
            <form method="POST" action="{{route('auth.password.forget')}}">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="email">ایمیل</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control mb-lg-2" id="email" value="{{old('email')}}"
                                aria-describedby="emailHelp" placeholder="ایمیل خود را وارد کنید">
                        </div>
                    </div>
                    <div class="col-sm-9 offset-sm-3">
                    @error('email')
                                    <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                        <strong>
                                            {{ $message }}
                                        </strong>
                                    </span>
                                @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">درخواست بازیابی رمز عبور</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

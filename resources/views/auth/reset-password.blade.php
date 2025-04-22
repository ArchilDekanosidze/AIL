@extends('layouts.master')
@section('head-tag')
<title>تغییر رمز عبور</title>
@endsection
@section('content')


<div class="row justify-content-center mt-lg-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
               تغییر رمز عبور
            </div>
            <div class="card-body">
            <form method="POST" action="{{route('auth.password.reset')}}">
                    @csrf
                <input type="hidden" name="token" value="{{$token}}">
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="email"> ایمیل </label>
                        <div class="col-sm-9">
                        <input type="email" name="email" class="form-control" id="email" readonly value="{{$email}}"
                                aria-describedby="emailHelp" placeholder="ایمیل خود را وارد کنید">
                        </div>
                    </div>
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="password">رمز عبور</label>
                        <div class="col-sm-9">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="رمز عبور خود را وارد کنید">
                        </div>
                    </div>
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="password_confirmation">تکرار رمز عبور</label>
                        <div class="col-sm-9">
                            <input type="password" name="password_confirmation" class="form-control"
                                id="password_confirmation" placeholder="تکرار رمز عبور">
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
                        @error('password')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
                        @error('password_confirmation')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">ثبت رمز جدید</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

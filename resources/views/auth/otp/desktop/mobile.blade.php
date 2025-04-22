@extends('layouts.master')

@section('head-tag')
<title>تغییر شماره موبایل</title>
@endsection


@section('content')
<!-- start body -->
<section class="">
    <section id="main-body-two-col" class="container-xxl body-container">
        <section class="row">




            <main id="main-body" class="main-body col-md-9">
                <section class="content-wrapper bg-white p-3 rounded-2 mb-2">

                <div class="card">
            <div class="card-header">
                        شماره موبایل جدید
            </div>
            <div class="card-body">
            <form method="POST" action="{{route('auth.otp.desktop.setting.mobile')}}">
                    @csrf
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="mobile">موبایل</label>
                        <div class="col-sm-9">
                            <input  name="mobile" class="form-control" id="mobile" value="{{old('mobile')}}"
                                 placeholder="شماره موبایل خود را وارد کنید">
                        </div>
                    </div>
                    <div class="col-sm-9 offset-sm-3">
                        @error('cantSendCode')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
                        @error('username')
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

                </section>
            </main>
        </section>
    </section>
</section>
<!-- end body -->
@endsection

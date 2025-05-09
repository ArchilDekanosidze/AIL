@extends('layouts.master')

@section('head-tag')
<title>تغییر ایمیل</title>
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
                        ایمیل جدید
            </div>
            <div class="card-body">
            <form method="POST" action="{{route('auth.otp.desktop.setting.email')}}">
                    @csrf
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="email">ایمیل</label>
                        <div class="col-sm-9">
                            <input  name="email" class="form-control" id="email" value="{{old('email')}}"
                                 placeholder="ایمیل جدید خود را وارد کنید">
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

                </section>
            </main>
        </section>
    </section>
</section>
<!-- end body -->
@endsection

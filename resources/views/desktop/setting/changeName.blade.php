@extends('layouts.master')

@section('head-tag')
<title>تغییر نام مستعار</title>
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
                        نام مستعار جدید
            </div>
            <div class="card-body">
            <form method="POST" action="{{route('auth.desktop.setting.changeName')}}">
                    @csrf
                    <div class="form-group row mb-lg-2">
                        <label class="col-sm-3 col-form-label" for="name">نام مستعار</label>
                        <div class="col-sm-9">
                            <input  name="name" class="form-control" id="name" value="{{old('name') ?? auth()->user()->name}}"
                                 placeholder="نام مستعار جدید خود را وارد کنید">
                        </div>
                    </div>
                    <div class="col-sm-9 offset-sm-3">
                        @error('name')
                            <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                <strong>
                                    {{ $message }}
                                </strong>
                            </span>
                        @enderror
                    </div>
                    <div class="offset-sm-3">
                    <button type="submit" class="btn btn-primary">تایید نام کاربری جدید</button>
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

@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/homeStyle.css')}}">
@endsection
@section('content')
<div class="userHome main-body">
    <div class="mainDivDirection">
        <a class="btn btn-primary" href="{{route('category.categoryQuestion.user.index', 1)}}">بانک سوالات</a>
        <a class="btn btn-primary">نمونه آزمون های کتبی</a>
        <a class="btn btn-primary">گام به گام</a>
        <a class="btn btn-primary" href="{{ route('freeQuestion.index') }}" >سوالات آزاد</a>
        <a class="btn btn-primary">جزوات درسی</a>
        <a class="btn btn-primary">کتب درسی</a>
    </div>
</div>
@endsection
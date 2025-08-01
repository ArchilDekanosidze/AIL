@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/homeStyle.css')}}">
@endsection
@section('content')
<div class="userHome main-body">
    <div class="mainDivDirection">
        <a class="btn btn-primary" href="{{route('chat.index')}}">گفت و گوی آنلاین</a>
        <a class="btn btn-primary" href="{{route('category.categoryQuestion.user.index', 1)}}">بانک سوالات</a>
        <a class="btn btn-primary" href="{{ route('freeQuestion.index') }}" >سوالات آزاد</a>
        <a class="btn btn-primary" href="{{route('category.categoryExam.index')}}">نمونه آزمون های کتبی</a>
        <a class="btn btn-primary" href="{{route('category.categoryGamBeGam.index')}}">گام به گام</a>
        <a class="btn btn-primary" href="{{route('category.categoryFree.index')}}">دسته بندی آزاد</a>
        <a class="btn btn-primary" href="{{route('category.categoryBook.index')}}">کتب درسی</a>
        <a class="btn btn-primary" href="{{route('category.categoryJozve.index')}}">جزوات درسی</a>
    </div>
</div>
@endsection
@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/desktop/desktop.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('admin.category.categoryQuestion.index', 1)}}">لیست دسته بندی های سوالات</a>
    </div>
    
    
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('admin.category.categoryQuestion.create')}}">ساخت  دسته بندی جدید برای سوالات</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('admin.question.create')}}">ساخت سوال تستی جدید</a>
    </div>

    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('admin.question.descriptive.create')}}">ساخت سوال تشریحی جدید</a>
    </div>

</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
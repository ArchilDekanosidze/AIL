@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/index.css')}}">
@endsection
@section('content')
<p class="breadcrump"> @php echo $path @endphp </p>
<div class="adminCategory main-body">
    <div class="mainDivDirection">
        @foreach($directCats as $directCat)
            <div class="btn">
            <a  class="btn-primary catName" href="{{route('admin.category.question.index', $directCat->id)}}">{{$directCat->name}} </a> 
            <div class="cardButtons">
                <a class="editLink" href="{{route('admin.category.question.edit', $directCat->id)}}">ادیت</a>
                <a class="editLink" href="{{route('admin.category.question.delete', $directCat->id)}}">حذف</a>
            </div>
            </div>             
        @endforeach                
    </div>
</div>
@endsection
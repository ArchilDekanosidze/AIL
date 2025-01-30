@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/category/question/create.css')}}">
@endsection
@section('content')
<form method="POST" action="{{route('admin.category.question.store')}}">
    @csrf
    <div class="adminCategory main-body">
        <div class="mainDivDirection">
            <select class="categorySelect" name="categorySelect">
            @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->path()}}</option>
            @endforeach      
            </select>   
            <input type="text" class="newCategory" name="newCategory">    
            <button class="save"> ذخیره</button>   
        </div>
    </div>
</form>
@endsection
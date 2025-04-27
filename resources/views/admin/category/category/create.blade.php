@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/category/categoryQuestion/create.css')}}">
@endsection
@section('content')
<form method="POST" action="{{route('admin.category.category.store')}}">
    @csrf
    <div class="adminCategory main-body">
        <div class="mainDivDirection">
            <select class="categorySelect" name="categorySelect">
            @foreach($categories as $category)
                        <option value="{{$category->id}}" {{session('categorySelect') == $category->id ? 'selected' : ''}}>{{$category->path()}}</option>
            @endforeach      
            </select>   
            <input type="text" class="newCategory" name="newCategory">    
            <button class="save"> ذخیره</button>   
        </div>
    </div>
</form>
@endsection
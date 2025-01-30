@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/category/question/edit.css')}}">
@endsection
@section('content')
<form method="POST" action="{{route('admin.category.question.update', $currentCategory->id)}}">
    @csrf
    <div class="adminCategory main-body">
        <div class="mainDivDirection">
            <select class="categorySelect" name="categorySelect">
                @foreach($categories as $category)
                    <option value="{{$category->id}}"  @php if($category->id == $currentCategory->parent->id) echo "selected" @endphp>{{$category->path()}}</option>
                @endforeach      
            </select>   
            <input type="text" class="currentCategory" name="currentCategory" value="{{$currentCategory->name}}">  
            <button class="save"> ذخیره</button>   
        </div>
    </div>
</form>
@endsection
@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/create.css')}}">
<script src="{{asset('assets/ckeditor/ckeditor.js')}}"></script>

@endsection
@section('content')
<form method="POST" action="{{route('admin.question.store')}}">
    @csrf
    <div class="adminCategory main-body">
        <div class="mainDivDirection">
            <select class="categorySelect" name="categorySelect">
            @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->path()}}</option>
            @endforeach      
            </select>                          
        </div>
        <div>
            <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
        </div>
        
        <div>
            <button class="save"> ذخیره</button>   
        </div>
    </div>
</form>
@endsection


@section('script')

<script>
	  CKEDITOR.replace( 'editor1' );
</script>

@endsection

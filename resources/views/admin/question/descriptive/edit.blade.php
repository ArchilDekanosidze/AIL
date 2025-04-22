@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/descriptive/edit.css')}}">

@endsection
@section('content')
<form method="POST" action="{{route('admin.question.descriptive.update', $question->id)}}">
    @csrf
    <div class="adminCategory main-body">
        <div class="mainDivDirection">
            <input type="hidden" name="qestionId" value="{{$question->id}}"/>
            <select class="categorySelect" name="categorySelect">
            @foreach($categories as $category)
                        <option value="{{$category->id}}" {{$category->id == $question->category_question_id ? "selected" :  ""}}> {{$category->path()}}</option>
            @endforeach      
            </select>                          
        </div>
        <div class="myTextArea">
            <div>
                <label for="editorFront">سوال</label>
                <textarea name="editorFront" id="editorFront" rows="4" cols="80">{!! $question->front !!}</textarea>
            </div>
            <div>
                <label for="editorBack">پاسخ</label>
                <textarea name="editorBack" id="editorBack" rows="4" cols="80">{!! $question->back !!}</textarea>
            </div>

            <div class="mytextBox">
                <label for="percentage">درصد سطح سوال: </label>
                <input type="text" name="percentage" class="percentage" value="{!! $question->percentage !!}">
            </div>
        </div>
        
        <div>
            <button class="save">ذخیره</button>   
        </div>
    </div>
</form>
@endsection


@section('scripts')
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> --}}

<script src="{{asset('assets/ckeditor/ckeditor.js')}}"></script>

<script>
    ClassicEditor.create(document.querySelector('#editorFront'), {});
    ClassicEditor.create(document.querySelector('#editorBack'), {});

        $(document).ready(function() {
        });
</script>

@endsection

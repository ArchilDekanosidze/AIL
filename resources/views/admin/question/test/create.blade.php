@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/test/create.css')}}">

@endsection
@section('content')
<form method="POST" action="{{route('admin.question.store')}}">
    @csrf
    <div class="adminCategory main-body">
        <div class="mainDivDirection">
            <select class="categorySelect" name="categorySelect">
            @foreach($categories as $category)
                        <option value="{{$category->id}}" {{session('categorySelect') == $category->id ? 'selected' : ''}}>{{$category->path()}}</option>
            @endforeach      
            </select>                          
        </div>
        <div class="myTextArea">
            <div>
                <label for="editorFront">سوال</label>
                <textarea name="editorFront" id="editorFront" rows="4" cols="80"></textarea>
            </div>
            <div>
                <label for="editorBack">پاسخ</label>
                <textarea name="editorBack" id="editorBack" rows="4" cols="80"></textarea>
            </div>
            <div>
                <label for="editorP1">گزینه اول</label>
                <textarea name="editorP1" id="editorP1" rows="4" cols="80"></textarea>
            </div>
            <div>
                <label for="editorP2">گزینه دوم</label>
                <textarea name="editorP2" id="editorP2" rows="4" cols="80"></textarea>
            </div>
            <div>
                <label for="editorP3">گزینه سوم</label>
                <textarea name="editorP3" id="editorP3" rows="4" cols="80"></textarea>
            </div>
            <div>
                <label for="editorP4">گزینه چهارم</label>
                <textarea name="editorP4" id="editorP4" rows="4" cols="80"></textarea>
            </div>
            <div class="mytextBox">
                <label for="answer">گزینه پاسخ: </label>
                <input type="text" name="answer" class="answer">
                <label for="percentage">درصد سطح سوال: </label>
                <input type="text" name="percentage" class="percentage">
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
    // ClassicEditor.create(document.querySelector('#editorFront'), {});
    // ClassicEditor.create(document.querySelector('#editorBack'), {});
    // ClassicEditor.create(document.querySelector('#editorP1'), {});
    // ClassicEditor.create(document.querySelector('#editorP2'), {});
    // ClassicEditor.create(document.querySelector('#editorP3'), {});
    // ClassicEditor.create(document.querySelector('#editorP4'), {});

        $(document).ready(function() {
        });
</script>

@endsection

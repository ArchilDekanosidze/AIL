@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/category/categoryBook/show.css')}}">
@endsection
@section('content')
<div class="CategoryBook main-body">   
    <div class="firstRow">
        <p>{{$data['title']}}</p>
    </div>
    <div class="secondRow">        
        <div class="rightSide">    
            <div class="field field-name-field-book-code">
                <div class="field-label">کد کتاب:&nbsp;</div>
                <div class="field-items">{{ $data['code'] }}</div>
            </div>

            <div class="field field-name-field-doreh">
                <div class="field-label">دوره تحصیلی:&nbsp;</div>
                <div class="field-items">
                    @foreach($data['allAncestorPath'] as $path)
                        <span class="lineage-item">{{ $path }}</span>
                    @endforeach
                </div>
            </div>

            <div class="field field-name-field-year">
                <div class="field-label">سال تحصیلی:&nbsp;</div>
                <div class="field-items">{{ $data['year'] }}</div>
            </div>
        </div>

        <div class="leftSide"> 
            <div class="field field-name-field-book-image">
                <a href="{{ route('books.show', $data['id']) }}">
                    <img src="{{ $data['image'] }}" alt="" width="230" height="320">
                </a>
            </div>
        </div>

    </div>    

    @if($data['parts'] != [])
        <div class="thirdRow field field-name-field-book-file-part">
            <div class="field-label">فایل بخش های کتاب:&nbsp;</div>
            <div class="field-items">
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>ضمیمه</th><th>حجم</th></tr></thead>
                    <tbody>
                        @foreach($data['parts'] as $part)
                        <tr>
                            <td>
                                <a href="{{ $part['url'] }}" target="_blank">{{ $part['name'] }}</a>
                            </td>
                            <td>{{ $part['size'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($data['fileUrl'] !=  null)
        <div class="forthRow field field-name-field-book-file">
            <div class="field-label">فایل کامل کتاب:&nbsp;</div>
            <div class="field-items">
                <table  class="table table-striped">
                    <thead class="table-dark"><tr><th>ضمیمه</th><th>حجم</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ $data['fileUrl'] }}" target="_blank">{{ $data['title'] }}</a>
                            </td>
                            <td>{{ $data['fileSize'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
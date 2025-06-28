@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/chat/groups/create.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection">
        <div class="chat-create-form-box">
            <h2 class="text-2xl font-bold mb-4">ساخت  {{$persianType }} جدید</h2>

            <form action="{{ route('chat.groups.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="form-group mb-3">
                    <label for="title" class="form-label font-semibold">اسم</label>
                    <input type="text" name="title" id="title" class="form-control w-full p-2 rounded border" required placeholder="نام {{ $persianType }} را وارد کنید">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold block">دسترسی</label>
                    <label>
                        <input type="radio" name="is_private" value="1" checked> خصوصی
                    </label>
                    <label class="ml-4">
                        <input type="radio" name="is_private" value="0"> عمومی
                    </label>
                </div>

                <div class="form-group mb-4">
                    <label for="link" class="form-label font-semibold">لینک دلخواه(اختیاری)</label>
                    <input type="text" name="link" id="link" class="form-control w-full p-2 rounded border" placeholder=" my-{{ $type }} :  مثلا">
                    <small class="text-gray-500">لینک به صورت مقابل استفاده میشود <code>/chat/{{ $type }}/your-link</code></small>
                </div>

                <button type="submit" class="mysubmitbtn bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">
                    ادامه
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.master')

@section('content')
<div class="CategoryBook main-body">
    <div class="mainDivDirection text-center mt-5">
        <div class="alert alert-warning">
            <h2 class="text-xl font-bold text-danger mb-3">دسترسی غیرمجاز</h2>
            <p class="text-gray-700">این کانال خصوصی است و فقط اعضا می‌توانند آن را مشاهده کنند.</p>
            <p class="text-gray-600 mt-2">برای دسترسی به این کانال، لطفاً از طریق لینک اختصاصی وارد شوید یا با مدیر تماس بگیرید.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-secondary mt-3">بازگشت به خانه</a>
    </div>
</div>
@endsection

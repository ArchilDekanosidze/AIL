@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/profile/student/profile.css')}}">
@endsection
@section('content')
<div class="userProfile main-body"> 

    پروفایل    {{ $user->name }}

</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
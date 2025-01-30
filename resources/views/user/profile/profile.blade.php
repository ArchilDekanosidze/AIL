@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/profile/profile.css')}}">
@endsection
@section('content')
<div class="userProfile main-body">
    <div class="profileCard">        
        <a class="btn btn-primary" href="{{route('user.learning.chooseCategory')}}">شروع آموزش</a>
    </div>
</div>
@endsection







@section('scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
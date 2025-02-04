@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/learning/onlineQuizInProgress/onlineQuizInProgress.css')}}">
@endsection
@section('content')
<div class="userLearningChooseCategory main-body">
    @if($errors->any())
        <h4 class="errorFromController">{{$errors->first()}}</h4>
    @endif

    <div class="question">
    </div>

    <div class="buttons">
        
    </div>

</div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {

            



        });
    </script>
@endsection
@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/quiz/result/result.css')}}">
@endsection
@section('content')
<div class="QuizResult main-body">
    <div class="finalPercentage">
        درصد شما در این آزمون:
        <span>{{$quiz->finalPercentage}}</span>
    <div>
        <canvas id="myChart"></canvas>
    </div>

</div>
@endsection



@section('scripts')
    <script src="{{asset('assets/js/chart.js')}}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script>
        $(document).ready(function() {
        
        const ctx = document.getElementById('myChart');



        new Chart(ctx, {
            type: 'pie',
            data: {
            labels: [ 'جواب داده نشده','غلط',  'صحیح' ],
            datasets: [{
                data: [{{$quiz->notAnswers}}, {{$quiz->wrongAnswers}}, {{$quiz->rightAnswers}}],
                borderWidth: 1,
                backgroundColor:['gray', 'red', 'green']
            }]
            },
            options: {
         
            }
        });
           

         
          


















            



        });
    </script>
@endsection
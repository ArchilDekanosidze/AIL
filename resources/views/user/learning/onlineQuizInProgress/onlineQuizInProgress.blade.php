@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/learning/onlineQuizInProgress/onlineQuizInProgress.css')}}">
@endsection
@section('content')
<div class="onlineQuizInProgress main-body">

    <input type="hidden" class="questionId" value="{{$question->id}}">
    <input type="hidden" class="quizId" value="{{$quiz->id}}">
    <input type="hidden" class="answerRetrived" value="0">


    <div class="questionDataDiv">
        <div class="questionFront">{{$question->front}}</div>
        <div class="pdiv p1">
            <input type="checkbox" class="pCheckBox p1CheckBox">
            {{$question->p1}}
        </div>
        <div class="pdiv p2">
            <input type="checkbox" class="pCheckBox p2CheckBox">
            {{$question->p2}}
        </div>
        <div class="pdiv p3">
            <input type="checkbox" class="pCheckBox p3CheckBox">
            {{$question->p3}}
        </div>
        <div class="pdiv p4">
            <input type="checkbox" class="pCheckBox p4CheckBox">
            {{$question->p4}}
        </div>
    </div>
    <div class="answerDiv">
    </div>
    
    
    <div class="quizInfo">
        <div class="questionCount">
            1/{{$quiz->count}}
        </div>
        <div class="timerMainDiv">
            مدت زمان باقیمانده: 
            <div class="timer" id="timer">               
            </div>
        </div>
    </div>
    
    
    <div class="buttons">
        <button class="prev btn disabled">قبلی</button>
        <button class="questionToggle btn">مشاهده پاسخ</button>
        <button class="next btn @if($quiz->count == 1) disabled @endif" >بعدی</button>
    </div>

</div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
            let timeLeft = {{$timeLeft}};
            @if($timeLeft>0)
                let timerInterval = setInterval(function(){
                    timeLeft--;
                    minutes = Math.floor(timeLeft/60);
                    seconds = timeLeft % 60;
                    seconds = seconds <10 ? "0" + seconds : seconds ;
                    $("#timer").text("  " +  minutes + " دقیقه و " + seconds + " ثانیه")
                    if(timeLeft <= 0)
                    {
                        clearInterval(timerInterval)
                        $(".failed-message").html("زمان آزمون به اتمام رسید")   
                        $('.failed-message').show().delay(5000).fadeOut('slow');
                        $(".questionDataDiv").addClass("quizDisabled");
                    }
                }, 1000)
            @else
                $(".questionDataDiv").addClass("quizDisabled");
            @endif

            $(".pdiv").click(function() {
                if($(this).find('.pCheckBox').is(":checked"))
                {
                    $(this).find('.pCheckBox').prop('checked', false);
                }
                else
                {
                    $('.pdiv .pCheckBox').prop('checked', false);
                    $(this).find('.pCheckBox').prop('checked', true);
                }
            })


            $(".pCheckBox").click(function(e) {
                e.stopPropagation();
                e.preventDefault();
            })

            $(".questionToggle").click(function(){  
                if($(this).text() == "مشاهده پاسخ")     
                {        
                    $(this).text("مشاهده سوال");
                    if($(".answerRetrived").val() == 0)
                    {                        
                        var url = "{{route('user.learning.quizInProgress.showAnswer')}}";        
                        data = {quizId:$(".quizId").val(),
                          questionId: $('.questionId').val(),
                          p1CheckBox : $(".p1CheckBox").is(":checked"),  
                          p2CheckBox : $(".p2CheckBox").is(":checked"),  
                          p3CheckBox : $(".p3CheckBox").is(":checked"),  
                          p4CheckBox : $(".p4CheckBox").is(":checked"),                      
                        }       
                        result = Ajax(url, data)
                        $(".answerDiv").text(result)
                        console.log(result)
                    }
                    $(".answerRetrived").val(1);
                    $(".questionDataDiv").hide();
                    $(".answerDiv").show();
                    $(".questionDataDiv .pdiv").addClass('disabled');
                }
                else if($(this).text() == "مشاهده سوال")     
                {  
                    $(this).text("مشاهده پاسخ")
                    $(".questionDataDiv").show();
                    $(".answerDiv").hide();
                }
                
            })




















            



        });
    </script>
@endsection
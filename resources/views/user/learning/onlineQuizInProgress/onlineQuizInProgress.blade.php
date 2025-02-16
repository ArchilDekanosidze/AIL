@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/learning/onlineQuizInProgress/onlineQuizInProgress.css')}}">
@endsection
@section('content')
<div class="onlineQuizInProgress main-body">

    <input  class="questionId" value="{{$question->id}}">
    <input type="hidden" class="quizQuestionId" value="{{$quizQuestion->id}}">
    <input type="hidden" class="quizId" value="{{$quiz->id}}">
    <input type="hidden" class="answerRetrived" value="0">
    <input type="hidden" class="allQuestionAnswered" value="{{$allQuestionAnswered}}">
    <input type="hidden" class="userAnswer" value="{{$quizQuestion->user_answer}}">
    <input type="hidden" class="questionAswer" value="{{$question->answer}}">


    <div class="questionDataDiv">
        <div class="questionFront">{{$question->front}}</div>
        <div class="pdiv p1">
            <input type="checkbox" class="pCheckBox p1CheckBox">
            <span class="p1Text">{{$question->p1}}</span>
        </div>
        <div class="pdiv p2">
            <input type="checkbox" class="pCheckBox p2CheckBox">
            <span class="p2Text">{{$question->p2}}</span>
        </div>
        <div class="pdiv p3">
            <input type="checkbox" class="pCheckBox p3CheckBox">
            <span class="p3Text">{{$question->p3}}</span>
        </div>
        <div class="pdiv p4">
            <input type="checkbox" class="pCheckBox p4CheckBox">
            <span class="p4Text">{{$question->p4}}</span>
        </div>
    </div>
    <div class="answerDiv">
    </div>
    
    
    <div class="quizInfo">
        <div class="questionCount">
            <span class="questionsPlace">{{$quizQuestion->place}}</span>/<span class="totalQuizQuestionCount">{{$quiz->count}}</span>
        </div>
        <div class="timerMainDiv">
            مدت زمان باقیمانده: 
            <div class="timer" id="timer">               
            </div>
        </div>
    </div>
    
    
    <div class="buttons">
        <button class="prev btn disabled">قبلی</button>
        <button class="questionToggle btn">ثبت و مشاهده پاسخ</button>
        <button class="next btn @if($quiz->count == 1) disabled @endif" >بعدی</button>
    </div>

</div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
            function changeColorBaseOnAnswer() {
                userAnswer = $(".userAnswer").val()
                questionAswer = $(".questionAswer").val()
                if(!userAnswer)
                {
                    return
                }          
                $(".p" + userAnswer).find(".pCheckBox").prop("checked", "checked")
                if(userAnswer == questionAswer)
                {
                    $(".p" + userAnswer).addClass("correctAnswer")   
                }
                else
                {
                    $(".p" + userAnswer).addClass("wrongAnswer")   
                    $(".p" + questionAswer).addClass("correctAnswer")   
                }
            }

            function newQuestion(result) {
                $(".questionToggle").text("ثبت و مشاهده پاسخ")
                $(".questionDataDiv").show();
                $(".answerDiv").hide();
                $(".questionDataDiv .pdiv").removeClass('disabled');
                
                $(".questionFront").html(result.question.front)
                $(".p1Text").html(result.question.p1)
                $(".p2Text").html(result.question.p2)
                $(".p3Text").html(result.question.p3)
                $(".p4Text").html(result.question.p4)
                $(".questionDataDiv .pCheckBox").prop('checked', false);
                $(".pdiv").removeClass("wrongAnswer")   
                $(".pdiv").removeClass("correctAnswer")  

                $(".quizQuestionId").val(result.quizQuestion.id)
                $(".questionId").val(result.question.id)
                $(".answerRetrived").val(0)
                $(".userAnswer").val(result.quizQuestion.user_answer)
                $(".questionAswer").val(null)
                $(".questionsPlace").text(result.quizQuestion.place)

                if(parseInt(result.quizQuestion.place) >1 )
                {
                    $(".buttons .prev").removeClass("disabled")
                }
                else
                {
                    $(".buttons .prev").addClass("disabled")
                }

                if(parseInt(result.quizQuestion.place) < parseInt($('.totalQuizQuestionCount').text()) )
                {
                    $(".buttons .next").removeClass("disabled")
                }
                else
                {
                    $(".buttons .next").addClass("disabled")
                }

            }


            if($(".allQuestionAnswered").val() == 1)
            {
                $(".questionDataDiv").addClass("quizDisabled");
            }
            changeColorBaseOnAnswer()

 

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
                if($(this).text() == "ثبت و مشاهده پاسخ")     
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
                    $(this).text("ثبت و مشاهده پاسخ")
                    $(".questionDataDiv").show();
                    $(".answerDiv").hide();
                }
                
            })

            $(".prev").click(function () {
                var url = "{{route('user.learning.quizInProgress.prevQuestion')}}";        
                        data = {quizId:$(".quizId").val(),
                        quizQuestionId: $('.quizQuestionId').val(),                                             
                        }       
                result = Ajax(url, data)
                if(result.errorMessages)
                {
                    alert(result.errorMessages);
                    return;
                }

                newQuestion(result)                
            })

            $(".next").click(function () {
                var url = "{{route('user.learning.quizInProgress.nextQuestion')}}";        
                        data = {quizId:$(".quizId").val(),
                        quizQuestionId: $('.quizQuestionId').val(),                                             
                        }       
                result = Ajax(url, data)
                if(result.errorMessages)
                {
                    alert(result.errorMessages);
                    return;
                }

                newQuestion(result)                
            })


















            



        });
    </script>
@endsection
@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/quiz/online/inProgress.css')}}">
@endsection
@section('content')
<div class="onlineQuizInProgress main-body">

    
    <input type="hidden" class="questionId" value="{{$question->id}}">
    <input type="hidden" class="quizQuestionId" value="{{$quizQuestion->id}}">
    <input type="hidden" class="quizId" value="{{$quiz->id}}">
    <input type="hidden" class="answerRetrived" value="0">
    <input type="hidden" class="allQuestionAnswered" value="{{$allQuestionAnswered}}">
    <input type="hidden" class="userAnswer" value="{{$quizQuestion->user_answer}}">
    <input type="hidden" class="questionAswer" value="{{$question->answer}}">


    <div class="questionDataDiv">
        <span class="questionIdForUser">(سوال شماره: {{$question->id}})</span>
        <div class="questionFront">{!! $question->front !!} </div>
        <div class="pdiv p1">
            <input type="checkbox" class="pCheckBox p1CheckBox">
            <span class="p1Text">{!! $question->p1 !!}</span>
        </div>
        <div class="pdiv p2">
            <input type="checkbox" class="pCheckBox p2CheckBox">
            <span class="p2Text">{!! $question->p2 !!}</span>
        </div>
        <div class="pdiv p3">
            <input type="checkbox" class="pCheckBox p3CheckBox">
            <span class="p3Text">{!! $question->p3 !!}</span>
        </div>
        <div class="pdiv p4">
            <input type="checkbox" class="pCheckBox p4CheckBox">
            <span class="p4Text">{!! $question->p4 !!}</span>
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
        <div class="nextPrevAnswer">
            <button class="prev btn disabled">قبلی</button>
            <button class="questionToggle btn">مشاهده پاسخ</button>
            <button class="next btn @if($quiz->count == 1) disabled @endif" >بعدی</button>
        </div>
        <div class="endQuizbuttons">
            <a href="{{route('quiz.online.saveOnlineQuizDataAndShowResult', $quiz->id)}}" class="endQuiz">ثبت و مشاهده نتیجه آزمون</a>
        </div>
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
                $(".questionToggle").text("مشاهده پاسخ")
                $(".questionDataDiv").show();
                $(".answerDiv").hide();
                $(".questionDataDiv .pdiv").removeClass('disabled');
                
                $(".questionIdForUser").html("(سوال شماره: " + result.question.id + ")")
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
                if(result.quizQuestion.user_answer)
                {
                    $(".questionAswer").val(result.question.answer)
                    $(".answerDiv").html(result.question.back)
                    $(".answerRetrived").val(1);
                    $(".questionDataDiv .pdiv").addClass('disabled');
                }
                else
                {
                    $(".questionAswer").val(null)
                }
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

                changeColorBaseOnAnswer()
                // MathBreak()

            }

            function createDataForAjax() {
                data = {quizId:$(".quizId").val(),
                    questionId: $('.questionId').val(),
                    quizQuestionId: $('.quizQuestionId').val(),
                    p1CheckBox : $(".p1CheckBox").is(":checked"),  
                    p2CheckBox : $(".p2CheckBox").is(":checked"),  
                    p3CheckBox : $(".p3CheckBox").is(":checked"),  
                    p4CheckBox : $(".p4CheckBox").is(":checked"),                      
                }       
                return data;
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
                if($(this).text() == "مشاهده پاسخ")     
                {        
                    $(this).text("مشاهده سوال");
                    if($(".answerRetrived").val() == 0)
                    {                        
                        var url = "{{route('quiz.online.showAnswer')}}";        
                        data = createDataForAjax();
                        result = Ajax(url, data)
                        $(".answerDiv").html(result)
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

                MathBreak()

                
            })



            $(".next").click(function () {
                var url = "{{route('quiz.online.nextQuestion')}}";        
                data = createDataForAjax();    
                result = Ajax(url, data)
                if(result.errorMessages)
                {
                    alert(result.errorMessages);
                    return;
                }

                newQuestion(result)                
            })

            $(".prev").click(function () {
                var url = "{{route('quiz.online.prevQuestion')}}";        
                data = createDataForAjax();  
                result = Ajax(url, data)
                if(result.errorMessages)
                {
                    alert(result.errorMessages);
                    return;
                }

                newQuestion(result)                
            })

            $(".endQuizbuttons a").click(function () {
                var url = "{{route('quiz.online.showAnswer')}}";        
                data = createDataForAjax();
                result = Ajax(url, data)
            })


            place = $(".questionsPlace").text()
            count = $(".totalQuizQuestionCount").text()
            if(place >1)
            {
                $(".prev").removeClass("disabled")
            }
            if(place == count)
            {
                $(".next").addClass("disabled")
            }













            



        });
    </script>
@endsection
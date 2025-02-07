@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/learning/new/chooseCategory.css')}}">
@endsection
@section('content')
<div class="userLearningChooseCategory main-body">
    @if($errors->any())
        <h4 class="errorFromController">{{$errors->first()}}</h4>
    @endif
    <form action="{{ route('user.learning.new.start') }}" method="post" >
        @csrf
        <ul>
            @foreach($allCategories as $category)
                @if($userCategories->contains($category)) 
                    <li class="catCheckBoxLi" style="margin-right : {{$category->depth *50}}px; @php  if($category->depth >1) echo 'display:none' @endphp" >                      
                        @if($category->descendants()->count() > 0 )
                        <span class="triangelForCategory">
                            <span class="toggle-icon">&#9664</span>
                        </span>
                        @endif
                        <input type="checkbox" name="categorySelected[]" class="catCheckBox"  value="{{$category->id}}" data-id="{{$category->id}}"> {{$category->name}}
                        @if($category->descendants()->count() == 0)
                            <div class="targetLevelDiv advancedSettingDiv">
                                <lable for="targetLevel"> درصد هدف:</lable>
                                <input class="targetLevel" id="targetLevel" name="targetLevels[{{$category->id}}]" type="number" min="0" max="100" value="{{$userCategories->find($category)->pivot->target_level}}">
                            </div>
                            <div class="currentLevelDiv advancedSettingDiv">
                                <lable for="currentLevel"> درصد فعلی:</lable>
                                <span class="currentLevel" id="currentLevel"  >{{$userCategories->find($category)->pivot->level}}</span>
                            </div>
                            <input type="hidden" name="currentLevels[{{$category->id}}]" min="0" max="100" value="{{$userCategories->find($category)->pivot->level}}">
                            <div class="number_to_change_levelDiv advancedSettingDiv">
                                <lable for="numbers_to_change_level"> تعداد آخرین سوالات در نظرگرفته شده برای محاسبه درصد فعلی:</lable>
                                <input class="numbers_to_change_level" id="numbers_to_change_level" name="numbers_to_change_level[{{$category->id}}]" type="number" min="5" max="100" value="{{$userCategories->find($category)->pivot->number_to_change_level}}">
                            </div>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>

        <div class="mainButton">
            <button class="startOnlineLearning btn" name="action" value="online">شروع آزمون آنلاین</button>
            <button class="startPaperLearning btn disabled" name="action" value="paper">پرینت آزمون کتبی</button>
            <a class="learningSetting btn">تنظیمات آزمون</a>
            <a class="advanceSetting btn">تنظیمات حرفه ای</a>
        </div>

        <div class="learningSettingDiv">
            <div class="quizNameDiv">
                <lable for="quizName"> نام آزمون:</lable>
                <input id="quizName" name="quizName" type="text">
            </div>
            <div class="testCountDiv">
                <lable for="testCount"> تعداد سوالات آزمون:</lable>
                <input id="testCount" name="testCount" type="number" min="0" max="150" value="40">
            </div>
            <div class="testTimeDiv">
                <lable for="testTime"> مدت زمان آزمون بر حسب دقیقه:</lable>
                <input id="testTime" name="testTime" type="number" min="0" max="300" value="60">
            </div>
        </div>
    </form>
</div>
@endsection



@section('scripts')
    <script>
        $(document).ready(function() {
            String.prototype.toNum = function(){
                return parseInt(this, 10);
            }

           

            $('.catCheckBox').click(function(){
                mainElm = $(this).parent()
                mainMargin = mainElm.css("marginRight").toNum()
                nextElm = mainElm.next()
                while(true)
                {
                    nextElmMargin = nextElm.css("marginRight").toNum()
                    if(nextElmMargin <= mainMargin)
                    {
                        return;
                    }
                    else
                    {
                        nextElm.find('.catCheckBox').prop('checked', mainElm.find('.catCheckBox').prop('checked'))
                    }
                    nextElm = nextElm.next()
                }
            })

            $('.triangelForCategory').click(function(){
                $(this).toggleClass('open')
                mainElm = $(this).parent()
                mainMargin = mainElm.css("marginRight").toNum()
                nextElm = mainElm.next()
                if($(this).hasClass('open'))
                {
                    $(this).find('.toggle-icon').html("&#9660")
                    while(true)
                    {
                        nextElmMargin = nextElm.css("marginRight").toNum()                       
                        if(nextElmMargin <= mainMargin)
                        {
                            return;
                        }
                        else
                        {
                            nextElm.find('.toggle-icon').html("&#9660")
                            nextElm.find(".triangelForCategory").addClass("open");
                            nextElm.show()
                        }
                        nextElm = nextElm.next()
                    }
                }
                else
                {
                    $(this).find('.toggle-icon').html("&#9664")
                    while(true)
                    {
                        nextElmMargin = nextElm.css("marginRight").toNum()
                        if(nextElmMargin <= mainMargin)
                        {
                            return;
                        }
                        else
                        {
                            nextElm.hide()
                        }
                        nextElm = nextElm.next()
                    }
                }
            })

            $(".learningSetting").click(function(){
                if($(this).text() == "تنظیمات آزمون")
                {
                    $(".learningSettingDiv").show()
                    $(this).text("عدم نمایش تنظیمات آزمون")
                }
                else if($(this).text() == "عدم نمایش تنظیمات آزمون")
                {
                    $(".learningSettingDiv").hide()
                    $(this).text("تنظیمات آزمون")
                }
            })

            $(".advanceSetting").click(function(){
                if($(this).text() == "تنظیمات حرفه ای")
                {
                    $(".advancedSettingDiv").show()
                    $(this).text("عدم نمایش تنظیمات حرفه ای")
                }
                else if($(this).text() == "عدم نمایش تنظیمات حرفه ای")
                {
                    $(".advancedSettingDiv").hide()
                    $(this).text("تنظیمات حرفه ای")
                }
            })

            



        });
    </script>
@endsection
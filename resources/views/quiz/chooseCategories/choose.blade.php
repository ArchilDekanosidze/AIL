@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/quiz/chooseCategories/choose.css')}}">
@endsection
@section('content')
<div class="userLearningChooseCategory main-body">

    <form  action="{{ route('quiz.create.student') }}" method="post" >
        @csrf
        <ul>
            @foreach($allCategories as $category)            
                @if($userCategories->contains($category))                        
                    <li class="catCheckBoxLi" data-parentId = {{$category->parent_id}} data-catId = {{$category->id}} style="--depth: {{$category->depth}}; @php  if($category->depth >1) echo 'display:none' @endphp" >                      
                        <div class="liDetails">
                            @if($category->descendants()->count() > 0 )
                                <span class="triangelForCategory">
                                    <span class="toggle-icon">&#9664</span>
                                </span>
                            @else
                                <p class="alignerLi" style="--depth: {{$category->depth-1}}""> </p>
                            @endif


                            <input type="checkbox"  name="categorySelected[]" class="catCheckBox"  value="{{$category->id}}" data-id="{{$category->id}}"> {{$category->name}}
                        </div>
                        @if($category->descendants()->count() == 0)
                            <div class="targetLevelDiv advancedSettingDiv">
                                <lable for="targetLevel"> درصد هدف:</lable>
                                <input class="targetLevel" id="targetLevel" name="targetLevels[{{$category->id}}]" type="number" min="0" max="100" value="{{$userCategories->find($category)->pivot->target_level}}">
                            </div>
                        @endif
                        <div class="currentLevelDiv advancedSettingDiv">
                            <lable for="currentLevel"> درصد فعلی:</lable>
                            <span class="currentLevel" id="currentLevel"  >{{$userCategories->find($category)->pivot->level}}</span>
                        </div>
                        <input type="hidden" name="currentLevels[{{$category->id}}]" min="0" max="100" value="{{$userCategories->find($category)->pivot->level}}">
                        <div class="number_to_change_levelDiv advancedSettingDiv">
                            <lable for="numbers_to_change_level"> تعداد آخرین سوالات در نظرگرفته شده برای محاسبه درصد فعلی:</lable>
                            <input class="numbers_to_change_level"  name="numbers_to_change_level[{{$category->id}}]" type="number" min="{{(6-$category->depth) * 25}}" max="1000" value="{{max($userCategories->find($category)->pivot->number_to_change_level,(6-$category->depth) * 25) }}" />
                        </div>
                        
                    </li>
                @endif
            @endforeach
        </ul>

        <div class="mainButton">
            <button class="startLearning startOnlineLearning btn" name="action" value="online">شروع آزمون آنلاین</button>
            <button  class="startLearning startPaperLearning btn disabled" name="action" value="paper">پرینت آزمون کتبی</button>
            <a class="learningSetting btn">تنظیمات آزمون</a>
            <a class="advanceSetting btn">تنظیمات حرفه ای</a>
        </div>

        <div class="learningSettingDiv">
            <div class="quizNameDiv learningSettingDirectDiv">
                <lable for="quizName"> نام آزمون:</lable>
                <input id="quizName" name="quizName" type="text">
            </div>
            <div class="testCountDiv learningSettingDirectDiv">
                <lable for="testCount"> تعداد سوالات آزمون:</lable>
                <input id="testCount" name="testCount" type="number" min="0" max="150" value="40">
            </div>
            <div class="testTimeDiv learningSettingDirectDiv">
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

            function toggleSubCategories(parentId, checked) {
                $(".catCheckBox").each(function () {
                    if($(this).parent().parent().data("parentid") ==  parentId)
                    {                        
                        $(this).prop('checked', checked)
                        toggleSubCategories($(this).parent().parent().data('catid'), checked)
                    }
                })
            }

            function hideSubCategories(catId)
            {
                $(".catCheckBoxLi").each(function () {
                        parentId= $(this).data('parentid')
                        if(parentId == catId)
                        {
                            hideSubCategories($(this).data('catid'))
                            $(this).hide()
                            $(this).find('.toggle-icon').html("&#9664")
                            $(this).find('.triangelForCategory').removeClass('open')
                        }
                    })
            }


            $('.catCheckBox').on("change", function(){

                catId = $(this).parent().parent().data("catid")
                isChecked = $(this).is(":checked")
                toggleSubCategories(catId, isChecked)
                
            })

            $('.triangelForCategory').click(function(){
                $(this).toggleClass('open')
                catId = $(this).parent().parent().data("catid")
                if($(this).hasClass('open'))                {
                    $(this).find('.toggle-icon').html("&#9660")
                    $(".catCheckBoxLi").each(function () {
                        parentId= $(this).data('parentid')
                        if(parentId == catId)
                        {
                            $(this).show()
                        }
                    })
                }
                else
                {
                    $(this).find('.toggle-icon').html("&#9664")                 
                    hideSubCategories(catId)
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

            $(".startLearning").click(function () {
                flag = false
                $(".numbers_to_change_level").each(function () {
                    if(parseInt($(this).val()) < parseInt($(this).attr('min')))
                    {
                        flag = true
                        $(this).parent().parent().addClass('minNumberRquire')
                    }
                })
                if(flag)
                {
                    $(".failed-message").html("حداقل سوالات برای تغییر سطح نمی تواند از حد مجاز کمتر باشد ")   
                    $('.failed-message').show().delay(5000).fadeOut('slow');
                }
            })
            $('.numbers_to_change_level').on("blur", function () {
                if($(this).val() < $(this).attr('min'))
                {
                    $(this).val($(this).attr('min'))   
                    $(".failed-message").html("حداقل سوالات برای تغییر سطح نمی تواند از حد مجاز کمتر باشد ")   
                    $('.failed-message').show().delay(5000).fadeOut('slow');

                }
            })

            $('.numbers_to_change_level').each(function() {
                if(parseInt($(this).val()) < parseInt($(this).attr('min')))
                {
                    $(this).val($(this).attr('min'))   
                }
            })


        });
    </script>
@endsection
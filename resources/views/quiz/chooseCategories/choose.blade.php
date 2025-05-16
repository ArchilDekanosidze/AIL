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
                            @if($category->descendants_count > 0 )
                                <span class="triangelForCategory">
                                    <span class="toggle-icon">&#9664</span>
                                </span>
                            @else
                                <p class="alignerLi" style="--depth: {{$category->depth-1}}""> </p>
                            @endif


                            <input type="checkbox"  name="categorySelected[]" class="catCheckBox"  value="{{$category->id}}" data-id="{{$category->id}}"> {{$category->name}}
                        </div>
                        @if($category->descendants_count == 0)
                            <div class="targetLevelDiv advancedSettingDiv">
                                <lable for="targetLevel"> درصد هدف:</lable>
                                <input class="targetLevel" id="targetLevel" name="targetLevels[{{$category->id}}]" type="number" min="0" max="100" value="{{$userCategories->find($category)->pivot->target_level}}">
                            </div>
                            <div class="number_to_change_levelDiv advancedSettingDiv">
                                <lable for="numbers_to_change_level"> آستانه:</lable>
                                <input class="numbers_to_change_level"  name="numbers_to_change_level[{{$category->id}}]" type="number" min="10"  value="{{$userCategories->find($category)->pivot->number_to_change_level}}" />
                            </div>
                        @endif
                        <div class="currentLevelDiv advancedSettingDiv">
                            <lable for="currentLevel"> درصد فعلی:</lable>
                            <span class="currentLevel" id="currentLevel"  >{{$userCategories->find($category)->pivot->level}}</span>
                        </div>
                        <input type="hidden" name="currentLevels[{{$category->id}}]" min="0" max="100" value="{{$userCategories->find($category)->pivot->level}}">

                        
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
                <input id="testCount" name="testCount" type="number" min="1" max="150" value="40">
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
        const userCategoriesHashSet = @json($userCategoriesHashSet);
    </script>

    <script>
        userCategoriesMap = @json($userCategories->keyBy('id')->map(function($item) {
            return [
                'target_level' => $item->pivot->target_level,
                'number_to_change_level' => $item->pivot->number_to_change_level,
                'level' => $item->pivot->level
            ];
        }));
    </script>
    <script>
        $(document).ready(function() {
            String.prototype.toNum = function(){
                return parseInt(this, 10);
            }

            function toggleSubCategories(parentId, checked) {
                // Select the current parent and toggle the checkboxes and subcategories recursively
                $(".catCheckBoxLi[data-parentid='" + parentId + "']").each(function () {
                    $(this).find('.catCheckBox').prop('checked', checked); // Update checkbox state
                    const childCatId = $(this).data('catid');
                    
                    // Recursively toggle subcategories
                    toggleSubCategories(childCatId, checked);
                });
            }

            function hideSubCategories(catId) {
                // Recursively hide all subcategories by using the catId
                $(".catCheckBoxLi[data-parentid='" + catId + "']").each(function () {
                    const childCatId = $(this).data('catid');
                    
                    // Hide the current category
                    $(this).hide();
                    
                    // Recurse for the subcategories
                    hideSubCategories(childCatId);
                    
                    // Reset UI elements for toggles
                    $(this).find('.toggle-icon').html("&#9664"); // Reset the toggle icon
                    $(this).find('.triangelForCategory').removeClass('open'); // Reset the open state
                });
            }

            
            $(document).on('change', '.catCheckBox', function(){

                catId = $(this).parent().parent().data("catid")
                isChecked = $(this).is(":checked")
                toggleSubCategories(catId, isChecked)
                
            })

            // $('.triangelForCategory').click(function(){
            //     $(this).toggleClass('open')
            //     catId = $(this).parent().parent().data("catid")
            //     if($(this).hasClass('open'))                {
            //         $(this).find('.toggle-icon').html("&#9660")
            //         $(".catCheckBoxLi").each(function () {
            //             parentId= $(this).data('parentid')
            //             if(parentId == catId)
            //             {
            //                 $(this).show()
            //             }
            //         })
            //     }
            //     else
            //     {
            //         $(this).find('.toggle-icon').html("&#9664")                 
            //         hideSubCategories(catId)
            //     }
            // })

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

            
            $(document).on('click', '.advanceSetting',function(){
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
           
            $(document).on('blur', '.numbers_to_change_level', function () {
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


            $(document).on('click', '.triangelForCategory',function() {
                const toggle = $(this);
                const li = toggle.closest('li');
                catId = $(this).parent().parent().data("catid")
                url = "{{ route('quiz.chooseCategories.getChildren') }}"
                data =  { 
                        parentId: catId ,
                        } ;

                // console.log(result)

                if (toggle.hasClass('open')) {
                    // Already open, so collapse
                    toggle.removeClass('open');
                    toggle.find('.toggle-icon').html("&#9664");
                    hideSubCategories(catId);
                    return;
                }


                if (li.attr('data-loaded') === 'true') {
                    toggle.addClass('open');
                    toggle.find('.toggle-icon').html("&#9660");

                    $(".catCheckBoxLi").each(function () {
                        if ($(this).data('parentid') == catId) {
                            $(this).show();
                        }
                    });
                    return;
                }

                toggle.toggleClass('open');
                toggle.find('.toggle-icon').html(toggle.hasClass('open') ? "&#9660" : "&#9664");

                if (toggle.hasClass('open')) {
                    // Fetch subcategories if not already loaded
                    subcategories = Ajax(url, data)  

                     mainLi = '';
                    subcategories.forEach(sub => {
                        if (!userCategoriesHashSet.hasOwnProperty(sub.id)) return; // skip unauthorized


                        const userData = window.userCategoriesMap?.[sub.id] || {};
                        const targetLevel = userData.target_level ?? 0;
                        const numberToChange = userData.number_to_change_level ?? Math.max(0, (6 - sub.depth) * 25);
                        const currentLevel = userData.level ?? 0;

                        subLi = `
                            <li class="catCheckBoxLi" data-parentId="${sub.parent_id}" data-catId="${sub.id}" style="--depth: ${sub.depth}">
                                <div class="liDetails">
                                    ${sub.descendants_count > 0
                                        ? `<span class="triangelForCategory"><span class="toggle-icon">&#9664</span></span>`
                                        : `<p class="alignerLi" style="--depth: ${sub.depth - 1}"></p>`}
                                    <input type="checkbox" name="categorySelected[]" class="catCheckBox" value="${sub.id}" data-id="${sub.id}"> ${sub.name}
                                </div>

                                ${sub.descendants_count == 0 ? `
                                    <div class="targetLevelDiv advancedSettingDiv">
                                        <label for="targetLevel"> درصد هدف:</label>
                                        <input class="targetLevel" id="targetLevel" name="targetLevels[${sub.id}]" type="number" min="0" max="100" value="${targetLevel}">
                                    </div>
                                    <div class="number_to_change_levelDiv advancedSettingDiv">
                                        <label for="numbers_to_change_level"> آستانه:</label>
                                        <input class="numbers_to_change_level" name="numbers_to_change_level[${sub.id}]" type="number" min="10" value="${numberToChange}" />
                                    </div>
                                ` : ''}

                                <div class="currentLevelDiv advancedSettingDiv">
                                    <label for="currentLevel"> درصد فعلی:</label>
                                    <span class="currentLevel" id="currentLevel">${currentLevel}</span>
                                </div>

                                <input type="hidden" name="currentLevels[${sub.id}]" min="0" max="100" value="${sub.level ?? 0}">
                            </li>
                        `;

                        mainLi = mainLi + subLi;
                    });
                    li.after(mainLi);  
                    li.attr('data-loaded', 'true');

                    // If advanced settings are currently visible, show them in the newly added elements
                    if ($('.advanceSetting').text() === "عدم نمایش تنظیمات حرفه ای") {
                        li.nextAll().each(function () {
                            if ($(this).data('parentid') == catId) {
                                $(this).find('.advancedSettingDiv').show();
                            }
                        });
                    }
                    
                    if (li.find('.catCheckBox').is(':checked')) {
                        // Auto-check all newly loaded subcategories
                        li.nextAll().each(function () {
                            if ($(this).data('parentid') == catId) {
                                $(this).find('.catCheckBox').prop('checked', true);
                            }
                        });
                    }
                } 
            })


        });
    </script>
@endsection
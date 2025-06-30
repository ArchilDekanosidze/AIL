@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/category/categoryQuestion/user/index.css')}}">
@endsection
@section('content')
<div class="userCategory main-body">    
    <input type="hidden" value="{{$currentCategory->id}}" class="currentCategoryId">
    <div class="breadCrump">
        @php
            for($i=0 ; $i <$ancestor->count() ; $i++) { 
                $currentAncestor = $ancestor[$i];
                echo "<a class='breadCrumpLink' href='" .  route('category.categoryQuestion.user.index', $currentAncestor->id) . "'>" . $currentAncestor->name . "</a>";
                if($i < $ancestor->count()-1)
                {
                    echo " -> ";
                }
            }            
        @endphp
    </div>
    <div class="mainDivDirection">
        @foreach($directCats as $directCat)
            <div class="categoryCard">
                <a class="btn btn-primary" href="{{route('category.categoryQuestion.user.index', $directCat->id)}}">{{$directCat->name}}</a> 
                
                @auth         
                    {{ $userStatus =  userCategoryStatus($directCat->id);}}
                    @if($userStatus == "all")
                        <button class="toggleCategoryUser toggleCategoryUserBtn" data-catid = "{{$directCat->id}}">حذف از لیست یادگیری</button>                      
                    @elseif($userStatus == "none")
                        <button class="toggleCategoryUser toggleCategoryUserBtn" data-catid = "{{$directCat->id}}">افزودن به لیست یادگیری</button>      
                    @else
                        <div class="toggleCategoryUserPartial">
                            <button class="toggleCategoryUserBtn" data-catid = "{{$directCat->id}}">افزودن زیردسته های انتخاب نشده به لیست یادگیری</button>
                            <button class="toggleCategoryUserBtn" data-catid = "{{$directCat->id}}">حذف زیر دسته های انتخاب شده از لیست یادگیری</button>
                        </div>
                    @endif   
                    
                @endauth
            </div>
        @endforeach                
    </div>
    <div class="mainQuestionDiv">
        <div class="buttons">
            <button class="toggleFrontAndBack">مشاهده پاسخ</button>
            <button class="nextRandomQuestion">سوال بعدی</button>
        </div>
        <div class="questionDataDiv">
            <div class="questionFront"></div>
            <div class="pdiv p1">
                <span class="p1Text"></span>
            </div>
            <div class="pdiv p2">
                <span class="p2Text"></span>
            </div>
            <div class="pdiv p3">
                <span class="p3Text"></span>
            </div>
            <div class="pdiv p4">
                <span class="p4Text"></span>
            </div>
        </div>
        <div class="answerDiv">
        </div>

    </div>
</div>
@endsection







@section('scripts')
    <script>
        function getNextQuestion()
        {
            var url = "{{ route('category.categoryQuestion.user.randomFreeQuestion.get') }}"   
            var data =  { currentCategoryId : $('.currentCategoryId').val()};    
   
            result = Ajax(url, data)
            $(".mainQuestionDiv .questionFront").html(result["front"])
            $(".mainQuestionDiv .p1").html(result["p1"])
            $(".mainQuestionDiv .p2").html(result["p2"])
            $(".mainQuestionDiv .p3").html(result["p3"])
            $(".mainQuestionDiv .p4").html(result["p4"])

            $(".mainQuestionDiv .answerDiv").html(result["back"])
            $(".mainQuestionDiv .questionDataDiv").addClass("show");
            $(".mainQuestionDiv .questionDataDiv").removeClass("hidden");
            $(".mainQuestionDiv .answerDiv").addClass("hidden");
            $(".mainQuestionDiv .answerDiv").removeClass("show");
            $(".toggleFrontAndBack").text("مشاهده پاسخ")

        }      
        
 

        $(document).ready(function() {
            getNextQuestion()
            $(".toggleFrontAndBack").click(function(){
                text = $(this).text()
                if(text == "مشاهده پاسخ")
                {
                    $(this).text("مشاهده سوال")
                    $(".mainQuestionDiv .questionDataDiv").addClass("hidden");
                    $(".mainQuestionDiv .questionDataDiv").removeClass("show");
                    $(".mainQuestionDiv .answerDiv").addClass("show");
                    $(".mainQuestionDiv .answerDiv").removeClass("hidden");
                }
                else
                {
                    $(this).text("مشاهده پاسخ")
                    $(".mainQuestionDiv .questionDataDiv").addClass("show");
                    $(".mainQuestionDiv .questionDataDiv").removeClass("hidden");
                    $(".mainQuestionDiv .answerDiv").addClass("hidden");
                    $(".mainQuestionDiv .answerDiv").removeClass("show");
                }
            })
            
            $(".nextRandomQuestion").click(function(){
                getNextQuestion();
            })

            $(".toggleCategoryUserBtn").click(function(){  
                elm = $(this);                  
                if(elm.text() =="افزودن به لیست یادگیری" || elm.text() =="افزودن زیردسته های انتخاب نشده به لیست یادگیری")
                {
                    var url = "{{ route('category.categoryQuestion.user.add_category_to_user') }}"   
                }
                else
                {
                    var url = "{{ route('category.categoryQuestion.user.remove_category_from_user') }}" 
                }
                var data =  { currentCategoryId :  $(this).data("catid")};     
                
                
                result = Ajax(url, data)

                if(result.responseJSON.message == "CSRF token mismatch.")
                {
                    window.location.href = "{{ route('home') }}"
                }

                if(elm.text() =="افزودن به لیست یادگیری"){
                    elm.text("حذف از لیست یادگیری")
                }
                else if(elm.text() =="حذف از لیست یادگیری")
                {
                    elm.text("افزودن به لیست یادگیری")
                }
                else if(elm.text() =="افزودن زیردسته های انتخاب نشده به لیست یادگیری")
                {
                    elm.text("حذف از لیست یادگیری")
                    elm.next().hide()
                }
                else if(elm.text() =="حذف زیر دسته های انتخاب شده از لیست یادگیری")
                {
                    elm.text("افزودن به لیست یادگیری")
                    elm.prev().hide()
                }
            })

            $(".remove_category_from_user").click(function(){                
                  
                var data =  { currentCategoryId :  $(this).data("catid")};          
                toggleCategoryUser("remove", url, data)               
            })



        });
    </script>
@endsection
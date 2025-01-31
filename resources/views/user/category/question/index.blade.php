@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/category/question/index.css')}}">
@endsection
@section('content')
<div class="userCategory main-body"> 
    <input type="hidden" value="{{$currentCategory->id}}" class="currentCategoryId">
    <div class="breadCrump">
        @php
            for($i=0 ; $i <$ancestor->count() ; $i++) { 
                $currentAncestor = $ancestor[$i];
                echo "<a class='breadCrumpLink' href='" .  route('category.question.index', $currentAncestor->id) . "'>" . $currentAncestor->name . "</a>";
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
                <a class="btn btn-primary" href="{{route('category.question.index', $directCat->id)}}">{{$directCat->name}}</a> 
                
                @auth         
                    @if(auth()->user()->userCategoryStatus($directCat->id) == "all")
                        <button class="toggleCategoryUser toggleCategoryUserBtn" data-catid = "{{$directCat->id}}">حذف از لیست یادگیری</button>                      
                    @elseif(auth()->user()->userCategoryStatus($directCat->id) == "none")
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
    <div class="Questiondiv">
        <div class="front"></div>
        <div class="back hidden"></div>
        <div class="buttons">
            <button class="toggleFrontAndBack">مشاهده پاسخ</button>
            <button class="nextRandomQuestion">سوال بعدی</button>
        </div>
    </div>
</div>
@endsection







@section('scripts')
    <script>
        function getNextQuestion()
        {
            var url = "{{ route('question.random.get') }}"   
            var data =  { currentCategoryId : $('.currentCategoryId').val()};          
            $.ajax({
                url: url,
                data:data,
                success: function(result) {
                    console.log(result)
                    if(result["error"])
                    {
                        alert(result["error"])
                    }
                    else
                    {
                        $(".Questiondiv .front").html(result["front"])
                        $(".Questiondiv .back").html(result["back"])
                        $(".Questiondiv .front").addClass("show");
                        $(".Questiondiv .front").removeClass("hidden");
                        $(".Questiondiv .back").addClass("hidden");
                        $(".Questiondiv .back").removeClass("show");
                        $(".toggleFrontAndBack").text("مشاهده پاسخ")
                    }
                }
            })
        }      
        
        function toggleCategoryUser(action, url, data)
        {
            alert(url)
            
        }

        $(document).ready(function() {
            getNextQuestion()
            $(".toggleFrontAndBack").click(function(){
                text = $(this).text()
                if(text == "مشاهده پاسخ")
                {
                    $(this).text("مشاهده سوال")
                    $(".Questiondiv .front").addClass("hidden");
                    $(".Questiondiv .front").removeClass("show");
                    $(".Questiondiv .back").addClass("show");
                    $(".Questiondiv .back").removeClass("hidden");
                }
                else
                {
                    $(this).text("مشاهده پاسخ")
                    $(".Questiondiv .front").addClass("show");
                    $(".Questiondiv .front").removeClass("hidden");
                    $(".Questiondiv .back").addClass("hidden");
                    $(".Questiondiv .back").removeClass("show");
                }
            })

            $(".nextRandomQuestion").click(function(){
                getNextQuestion();
            })

            $(".toggleCategoryUserBtn").click(function(){  
                elm = $(this);                  
                if(elm.text() =="افزودن به لیست یادگیری" || elm.text() =="افزودن زیردسته های انتخاب نشده به لیست یادگیری")
                {
                    var url = "{{ route('question.add_category_to_user') }}"   
                }
                else
                {
                    var url = "{{ route('question.remove_category_from_user') }}" 
                }
                var data =  { currentCategoryId :  $(this).data("catid")};                                     
                $.ajax({
                    url: url,
                    data:data,
                    success: function(result) {
                        console.log(result)
                        if(result["error"])
                        {
                            alert(result["error"])
                        }
                        else
                        {
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
                        }
                    }
                })
            })

            $(".remove_category_from_user").click(function(){                
                  
                var data =  { currentCategoryId :  $(this).data("catid")};          
                toggleCategoryUser("remove", url, data)               
            })



        });
    </script>
@endsection
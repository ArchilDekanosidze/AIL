@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/user/learning/chooseCategory.css')}}">
@endsection
@section('content')
<div class="userLearningChooseCategory main-body">
    <ul>
        @foreach($allCategories as $category)
            @if($userCategories->contains($category))                                                
                <li class="catCheckBoxLi" style="margin-right : {{$category->depth *50}}px; @php  if($category->depth >1) echo 'display:none' @endphp" >                      
                    @if($category->descendants()->count() > 0 )
                    <span class="triangelForCategory">
                        <span class="toggle-icon">&#9664</span>
                    </span>
                    @endif
                    <input type="checkbox" class="catCheckBox"  data-id="{{$category->id}}"> {{$category->name}}
                </li>
            @endif
        @endforeach
    </ul>
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




        });
    </script>
@endsection
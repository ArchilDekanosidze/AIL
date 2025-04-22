@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/freeQuestion/index.css')}}">
@endsection
@section('content')
<div class="userFreeQuestion main-body"> 

    <div id="question-section">

    </div>

    <button id="load-more" data-page="1"  class="btn btn-primary">
        مشاهده بیشتر
    </button>
    @auth        
        <div class="newQuestionDiv" id="newQuestionDiv">
            <label for="newQuestionHead">موضوع :</label>
            <input type="text" id="newQuestionHead" name="newQuestionHead" class="newQuestionHead"> 
            <label for="newQuestion">سوال جدید:</label>
            <textarea name="newQuestion" class="newQuestion" id="newQuestion" rows="4" cols="80"></textarea>
            <div class="mainTagDiv">
                <input name="tags" id="tags-input" placeholder="افزودن تگ جدید..." />
                <small id="tag-count" class="text-sm text-gray-500">0 / 5 تگ</small>
            </div>
            <button class="saveNewQuestion btn btn-success">ذخیره</button>   
        </div>
    @endauth
    @guest
        <div class="loginButtn"><a href="{{route('auth.login.form')}}" class="btn btn-primary">لطفا برای ثبت نظر ابتدا وارد سایت شوید</a></div>
    @endguest
   

</div>
@endsection







@section('scripts')

@include('partials.ckeditor-setup')

<script type="module">
            document.addEventListener('DOMContentLoaded', () => {
        window.initializeEditor('#newQuestion').then(editor => {
            window.editorInstance = editor;
        });
    
    });
</script>




<script>
    function createFreeQuestionString(freeQuestion) {
        string = "";
        console.log(freeQuestion);
        
            string = string + `<div class="freeQuestionMainCol">`
                string = string + `<div class="row1"> `
                    string = string + `<a href="${freeQuestion.user.profile_url}">${freeQuestion.user.name}</a> `
                    string = string + `<span class="freeQuestionScore">امتیاز: ${freeQuestion.user.score}</span>`
                    string = string + `<span class="freeQuestionVote">رای: ${freeQuestion.voteCount}</span>`
                    string = string + `<span class="freeQuestionComment">نظر: ${freeQuestion.commentCount}</span>`

                    for (index = 0; index < freeQuestion.tags.length; index++) {
                        string = string + `<span class="freeQuestionTgas">${freeQuestion.tags[index]['name']}</span>`                            
                    }

                string = string + `</div>`   
                string = string + `<div class="row2"> `
                    string = string +  `<div class="freeQuestionBody"><a href="${freeQuestion.showUrl}">${freeQuestion.head}</a></div>`
                string = string + `</div>`   
            string = string + `</div>`       
        return string;
    }

    $(document).ready(function() {
        lastQuestionId = 0;
        function loadQuestions() {
            var button = $("#load-more");
            var page = button.data("page");

            var url = "{{route('freeQuestion.fetchFreeQuestions')}}";        
            data =  { 
                lastQuestionId: lastQuestionId ,
                    } ;
            freeQuestions = Ajax(url, data)  
            console.log(freeQuestions)

            if (freeQuestions.length > 0) 
            {

                string = "";

                $.each(freeQuestions, function (index, comment) {
                    if (!$(`#comment-${comment.id}`).length) { 
                        string = string + createFreeQuestionString(comment);
                    }
                });

                $("#question-section").append(string)                                 

                lastQuestionId = freeQuestions[freeQuestions.length - 1].id;
                if (freeQuestions.length < 5) {
                    button.hide();
                }
            }
            else
            {

                button.hide(); // Hide button when no more comments
            }

        }    

        loadQuestions();

        $("#load-more").on("click", function () {
            loadQuestions();
        });


        $(".saveNewQuestion").click(function () {
            var url = "{{route('freeQuestion.newQuestion')}}";   
            const selectedTags = tagify.value.map(tag => tag.value);
            if (selectedTags.length < 1) {                    
                $(".failed-message").html("لطفا حداقل یک تگ انتخاب کنید")   
                $('.failed-message').show().delay(5000).fadeOut('slow');
                return;
            }
            data = {freeQuestion_head : $(".newQuestionHead").val(),
                freeQuestion_body: window.editorInstance.getData(),      
                selectedTags : selectedTags                   
                    } ;
            result = Ajax(url, data)  
            checkUnauthenticated(result, "لطفا برای ثبت نظر ابتدا وارد شوید")
            console.log(result);        
            if(result.error)
            {
                $(".failed-message").html(result.error)   
                $('.failed-message').show().delay(5000).fadeOut('slow');
            }
            if(result.successMessages)
            {
                $(".success-message").html(result.successMessages)   
                $('.success-message').show().delay(5000).fadeOut('slow');
                window.editorInstance.setData("");
                tagify.removeAllTags();
                $(".ck-content").empty()
                $(".newQuestionHead").val("")
                $("#question-section").prepend(createFreeQuestionString(result.freeQuestion))                    
                $("html, body").animate({scrollTop: $("#question-section").offset().top },500,)}
            }
        )

        const existingTags = @json($freeTags->pluck('name'));

        const input = document.querySelector('#tags-input');
        const tagify = new Tagify(input, {
            maxTags: 5, 
            duplicates: false,
            whitelist: existingTags,
            dropdown: {
                enabled: 0,         // show suggestions on focus
                closeOnSelect: false
            },
            enforceWhitelist: false // allow new tags not in the list
        });

        function updateTagCount() {
            document.getElementById('tag-count').textContent = `${tagify.value.length} / 5 تگ`;
        }
        tagify.on('add', updateTagCount);
        tagify.on('remove', updateTagCount);

    


        tagify.on('invalid', function(e) {
            if(e.detail.message == "number of tags exceeded")
            {
                $(".failed-message").html("شما بیشتر از ۵ تگ برای یک سوال نمی توانید انتخاب کنید")   
                $('.failed-message').show().delay(2000).fadeOut('slow');   
            }
        });
                    
    });
    </script>
@endsection
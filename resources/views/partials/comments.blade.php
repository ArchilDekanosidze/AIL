<link rel="stylesheet" href="{{asset('assets/css/partials/comment.css')}}">

<div >
    <div id="comment-section">

    </div>

    <button id="load-more" data-page="1" data-question="{{ $question->id }}" class="btn btn-primary">
       مشاهده بیشتر
    </button>
    @auth        
        <div class="newCommentDiv" id="newCommentDiv">
            <input type="hidden" class="parent_comment_id" name="parent_comment_id"  value="">
            <label for="newComment">کامنت جدید:</label>
            <textarea name="newComment" class="newComment" id="newComment" rows="4" cols="80"></textarea>
            <button class="saveNewComment btn btn-success">ذخیره</button>   
        </div>
    @endauth
    @guest
        <div class="loginButtn"><a href="{{route('auth.login.form')}}" class="btn btn-primary">لطفا برای ثبت نظر ابتدا وارد سایت شوید</a></div>
    @endguest
</div>



   

@section('scripts2')
@include('partials.ckeditor-setup')

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        window.initializeEditor('#newComment')
            .then(editor => {
                window.editorInstance = editor;
            })
            .catch(error => {
                console.error('Failed to initialize CKEditor:', error);
            });
    });
</script>



    <script>
        let lastCommentId = null; // Store last comment ID
        function replyTo(commentId) {

            $("html, body").animate(
                {
                    scrollTop: $("#newCommentDiv").offset().top  // Adjust offset if needed
                },
                500,)

            $(".parent_comment_id").val(commentId)   
        }
        function createCommentString(comment) {
            string = "";
            string = string + `<div class="commentMainCol">`
                string = string + `<div class="col1">`
                    @auth                        
                        if({{auth()->id()}} !== comment.user.id)
                        {                        
                            string = string +  `<button class="vote-btn" data-commentid="${comment.id}" data-votetype="up">⬆️</button> `
                        }                    
                        string = string +  `<span class="comment-score" id="comment-score-${comment.id}">${comment.score ?? 0}</span> `                    
                        if({{auth()->id()}} !== comment.user.id)
                        {
                            string = string + `<button class="vote-btn" data-commentid="${comment.id}"  data-votetype="down">⬇️ </button> `
                        }
                        if(comment.canMarkAsBest)
                        {
                            if(comment.best_reply_id == comment.id)
                            {                                    
                                string = string + `<button class="best-reply-btn" data-isbest="yes" data-commentid="${comment.id}" data-commentoriginalid="${comment.original_id}"> `
                                string = string + '✅ بهترین پاسخ' 
                            }
                            else
                            {
                                string = string + `<button class="best-reply-btn" data-isbest="no" data-commentid="${comment.id}" data-commentoriginalid="${comment.original_id}"> `
                                string = string + 'انتخاب به عنوان بهترین پاسخ' 
                            }
                            string = string + `</button>`
                        }
                    @endauth
                string = string +  `</div>`
                string = string + `<div class="col2">` 
                    string = string +  `<div id="comment-${comment.id}" class="comments">` 
                        if(comment.parent.id != null)
                        {
                            string = string + `<div class="comment_header" > ` ;
                            string = string +  `<p><small>پاسخ به <a href="${comment.parent.profile_url}">${comment.parent.user_name}</a> : <a class="reply-link" href="#comment-${comment.parent.id}">"${comment.parent.body}" </a></small></p> `
                            string = string + `</div>`
                        }
                        string = string + `<div class="comment_body" > ` ;
                            string = string + `<div><strong><a href="${comment.user.profile_url}">${ comment.user.name}</a></strong> :</div><div> ${comment.body}</div> `
                        string = string +  `</div>`
                        string = string + `<a href="#newComment" onclick="replyTo(${comment.id })"> پاسخ </a>`
                    string = string +  `</div>`
                string = string +  `</div>`
            string = string +  `</div>`
            return string;
        }
        $(document).ready(function() {
            function loadComments() {
                var button = $("#load-more");
                var page = button.data("page");
                var questionId = button.data("question");

                var url = "{{route('question.comment.fetchComments')}}";        
                data =  { last_comment_id: lastCommentId ,
                        question_id: questionId,                         
                        } ;
                comments = Ajax(url, data)  
                console.log(comments)
                console.log(page)

                if (comments.length > 0) 
                {

                    string = "";

                    $.each(comments, function (index, comment) {
                        if (!$(`#comment-${comment.id}`).length) { 
                            string = string + createCommentString(comment);
                        }
                    });

                    $("#comment-section").append(string)

                    let isBest = $(".best-reply-btn").each(function() {
                       let isBest = $(this).attr("data-isbest")
                        $(this).removeAttr("data-isbest")
                        .attr("data-isbest", isBest)
                        .data("isbest", isBest);
                        
                    })


                    rebindReplayLink()
                    rebindBestReplay()
                    rebindVote()


                    lastCommentId = comments[comments.length - 1].id;
                    if (comments.length < 5) {
                        button.hide();
                    }
                }
                else
                {

                    button.hide(); // Hide button when no more comments
                }

            }    

            loadComments();


            $("#load-more").on("click", function () {
                loadComments();
            });





            function rebindReplayLink() {                                        
                $(".reply-link").off("click").on("click", function (e) {
                    e.preventDefault();
                    var targetId = $(this).attr("href"); // Get the href value (e.g., "#comment-5")
                    var targetElement = $(targetId);
                    
                    function gotoParent() {
                        var targetElement = $(targetId);
                        $("html, body").animate(
                            {
                                scrollTop: targetElement.offset().top - 50 // Adjust offset if needed
                            },
                            500, // Scroll speed (milliseconds)
                            function () {
                                // Highlight the target comment
                                targetElement.css("background-color", "#ffff99");
                                setTimeout(function () {
                                    targetElement.css("background-color", "");
                                }, 2000);
                            }
                        );
                    }
                    
                
                    if (targetElement.length) {
                        gotoParent()
                    }
                    else
                    {
                        function loadUntilParent(targetId) {
                            var targetElement = $(targetId);

                            if (targetElement.length) {
                                gotoParent()
                            }
                            else
                            {
                                loadComments();
                                loadUntilParent(targetId);
                            }
                        }
                        loadUntilParent(targetId);
                    }
                });

            }
            rebindReplayLink()

            $(".saveNewComment").click(function () {
                var url = "{{route('question.comment.newComments')}}";        
                data = {question_id : $("#load-more").data("question"), 
                        parent_comment_id : $(".parent_comment_id").val(),
                        comment_body: window.editorInstance.getData(),                         
                        } ;
                result = Ajax(url, data)  
                checkUnauthenticated(result, "لطفا برای ثبت نظر ابتدا وارد شوید")

                $(".success-message").html(result.successMessages)   
                $('.success-message').show().delay(5000).fadeOut('slow');
                window.editorInstance.setData("");
                $(".parent_comment_id").val("")
                $("#comment-section").prepend(createCommentString(result.comment))

                $("html, body").animate({scrollTop: $("#comment-section").offset().top },500,)}
            )
            function rebindVote() {
                
                $('.vote-btn').off("click").click(function() {
                    var url = "{{route('question.comment.vote')}}";        
                    data = {commentId :$(this).data("commentid"), 
                        voteType : $(this).data("votetype"),
                    } ;
                    result = Ajax(url, data)  
                    console.log(result)

                    checkUnauthenticated(result, "لطفا برای ثبت رای ابتدا وارد شوید")
                    if(result.errorSelfvoting)
                    {
                        $(".failed-message").html(result.errorSelfvoting)   
                        $('.failed-message').show().delay(5000).fadeOut('slow');
                    }
                    if(result.success)
                    {
                        $(".success-message").html(result.success)   
                        $('.success-message').show().delay(5000).fadeOut('slow');
                        $(this).parent().find('.comment-score').html(result.vote)
                    }
                })
            }  
            // rebindVote()          
            function rebindBestReplay() {
                
                $(".best-reply-btn").off("click").click(function() {

                    
                    elem = $(this)
                    let isBest = elem.prop("data-isbest") === "yes" ? "no" : "yes";

                    let originalId = elem.attr("data-commentoriginalid"); // Get original thread ID
                    $(`.best-reply-btn[data-commentoriginalid="${originalId}"]`)
                    .prop("data-isbest", "no")  // Convert all to "no"
                    .data("isbest", "no");
                    $(`.best-reply-btn[data-commentoriginalid="${originalId}"]`)
                    .attr("data-isbest", "no")

                    $(`.best-reply-btn[data-commentoriginalid="${originalId}"]`).html("انتخاب به عنوان بهترین پاسخ");


                    elem.prop("data-isbest", isBest).data("isbest", isBest);
                    elem.attr("data-isbest", isBest)

                    if(elem.data("isbest")== "yes")
                    {
                        elem.html("✅ بهترین پاسخ")
                    }
                    if(elem.data("isbest") == "no")
                    {
                        elem.html("انتخاب به عنوان بهترین پاسخ")
                    }
                  
                    var url = "{{route('question.comment.best-reply')}}";        
                    data = {commentId :elem.data("commentid"),
                            isBest : elem.data('isbest')
                    } ;
                    console.log(data)
                    result = Ajax(url, data)  
                    if(result.error)
                    {
                        $(".failed-message").html(result.errorSelfvoting)   
                        $('.failed-message').show().delay(5000).fadeOut('slow');
                    }
                    checkUnauthenticated(result, "لطفا برای ثبت بهترین پاسخ ابتدا وارد شوید")
                    if(result.success)
                    {
                        $(".success-message").html(result.success)   
                        $('.success-message').show().delay(5000).fadeOut('slow');
                        $(this).parent().find('.comment-score').html(result.vote)
                    }

                })
            }
            // rebindBestReplay()

        });
    </script>
@endsection
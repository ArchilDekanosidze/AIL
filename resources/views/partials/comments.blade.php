<link rel="stylesheet" href="{{asset('assets/css/partials/comment.css')}}">

<div >
    <div id="comment-section">

    </div>

    <button id="load-more" data-page="1" data-question="{{ $question->id }}" class="btn btn-primary">
       مشاهده بیشتر
    </button>

    <div class="newCommentDiv" id="newCommentDiv">
        <input type="hidden" class="parent_comment_id" name="parent_comment_id"  value="">
        <label for="newComment">کامنت جدید:</label>
        <textarea name="newComment" class="newComment" id="newComment" rows="4" cols="80"></textarea>
        <button class="saveNewComment btn btn-success">ذخیره</button>   
    </div>
</div>



   

@section('scripts2')
    <script src="{{asset('assets/ckeditor/ckeditor.js')}}"></script>
    <script>
        ClassicEditor.create(document.querySelector('#newComment'), { })
                      .then(editor=>{
                        editorInstance = editor
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
                    // auth                        
                        // if({{auth()->id()}} !== comment.user.id)
                        {                        
                            string = string +  `<button class="vote-btn" data-commentid="${comment.id}" data-votetype="up">⬆️</button> `
                        }                    
                        string = string +  `<span class="comment-score" id="comment-score-${comment.id}">${comment.score}</span> `                    
                        // if({{auth()->id()}} !== comment.user.id)
                        {
                            string = string + `<button class="vote-btn" data-commentid="${comment.id}"  data-votetype="down">⬇️ </button> `
                        }
                    // endauth
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

                    rebindReplayLink()


                    lastCommentId = comments[comments.length - 1].id;
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
                $(".reply-link").on("click", function (e) {
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
                        comment_body: $(".ck-content").html(),                         
                        } ;
                result = Ajax(url, data)  
                $(".success-message").html(result.successMessages)   
                $('.success-message').show().delay(5000).fadeOut('slow');
                editorInstance.setData("");
                $(".ck-content").empty()
                $(".parent_comment_id").val("")
                $("#comment-section").prepend(createCommentString(result.comment))

                $("html, body").animate({scrollTop: $("#comment-section").offset().top },500,)}
            )

            $('.vote-btn').click(function() {
                var url = "{{route('comment.vote')}}";        
                data = {commentId :$(this).data("commentid"), 
                        voteType : $(this).data("votetype"),
                        } ;
                result = Ajax(url, data)  
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
        });
    </script>
@endsection
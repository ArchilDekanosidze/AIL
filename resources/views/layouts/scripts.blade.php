<script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
<script src="{{asset('assets/js/popper.js')}}"></script>
<script src="{{asset('assets/js/bootstrap/bootstrap.bundle.js')}}"></script>
<script src="{{asset('assets/js/grid.js')}}"></script>
<script src="{{asset('assets/select2/js/select2.min.js')}}"></script>
<script src="{{asset('assets/sweetalert/sweetalert2.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
    function Ajax(url, data)
    {
        data =  $.extend(data, {"_token": "{{ csrf_token() }}"}); 
        Myresult = ""
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            async: false,
            success: function(result) {
                Myresult =  result
                },
            error : function(result) {
                Myresult =  result
            }    
        })
        return Myresult
    }

    function AjaxGet(url, data)
    {
        data =  $.extend(data, {"_token": "{{ csrf_token() }}"}); 
        Myresult = ""
        $.ajax({
            method: "GET",
            url: url,
            data: data,
            async: false,
            success: function(result) {
                Myresult =  result
                }
        })
        return Myresult
    }
    
    // function MathBreak() {
    //     document.querySelectorAll('mstyle').forEach(el => {
    //         const newEl = document.createElement('p');
    //         newEl.className = 'math-container';
    //         newEl.innerHTML = el.innerHTML; // Copy the content
    //         el.replaceWith(newEl); // Replace the custom tag
    //     }); 
    // }

    $(document).ready(function() {
        $('.errorFromController').delay(5000).fadeOut('slow');
        $('.successFromController').delay(5000).fadeOut('slow');
    })

    function checkUnauthenticated(response, message)
    {
        if(response.statusText == "Unauthorized")
        {
            $(".failed-message").html(message)   
            $('.failed-message').show().delay(5000).fadeOut('slow');
        }
    }
</script>


<script>
function updatePagination() {
    const paginations = document.querySelectorAll('.pagination');

    paginations.forEach(ul => {
        const pageItems = ul.querySelectorAll('li.page-item');
        const width = window.innerWidth;
        const isMobile = width < 576;

        // Find the active page index (excluding prev/next)
        let activeIndex = -1;

        pageItems.forEach((item, i) => {
            if (item.classList.contains('active')) {
                activeIndex = i;
            }
        });

        pageItems.forEach((item, i) => {
            const isPrev = item.innerText.includes('قبلی') || item.querySelector('a')?.innerText.includes('قبلی');
            const isNext = item.innerText.includes('بعدی') || item.querySelector('a')?.innerText.includes('بعدی');

            if (!isMobile) {
                // Show everything on large screens
                item.style.display = '';
                return;
            }

            // Always show prev/next
            if (isPrev || isNext) {
                item.style.display = '';
                return;
            }

            // Show only current page, and its neighbors
            if (Math.abs(i - activeIndex) <= 1) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}


    document.addEventListener('DOMContentLoaded', updatePagination);
    window.addEventListener('resize', updatePagination);
</script>






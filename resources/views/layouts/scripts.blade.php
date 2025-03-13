<script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
<script src="{{asset('assets/js/popper.js')}}"></script>
<script src="{{asset('assets/js/bootstrap/bootstrap.bundle.js')}}"></script>
<script src="{{asset('assets/js/grid.js')}}"></script>
<script src="{{asset('assets/select2/js/select2.min.js')}}"></script>
<script src="{{asset('assets/sweetalert/sweetalert2.min.js')}}"></script>

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
                    }
            })
            return Myresult
        }
        $(document).ready(function() {
            $('.errorFromController').delay(5000).fadeOut('slow');
            $('.successFromController').delay(5000).fadeOut('slow');


    })
    function MathBreak() {
        document.querySelectorAll('mstyle').forEach(el => {
            const newEl = document.createElement('p');
            newEl.className = 'math-container';
            newEl.innerHTML = el.innerHTML; // Copy the content
            el.replaceWith(newEl); // Replace the custom tag
        }); 
    }
</script>

{{-- <script type="text/javascript" src="{{asset('assets/MathJax.js')}}"></script>

<script type="text/x-mathjax-config;executed=true">
    MathJax.Hub.Config({
    tex2jax: { inlineMath: [["$","$"],["\\(","\\)"]] },
    "HTML-CSS": {
    linebreaks: { automatic: false, width: "container" },
    scale: 120,
     mtextFontInherit: true
    }
    });

</script> --}}


<script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
      },
      "HTML-CSS": {
            linebreaks: { automatic: false, width: "container" },
            scale: 120,
            mtextFontInherit: true
            }
        });
    };
</script>
{{-- <script async src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    
<script>

MathJax.typesetPromise().then(function() {
    document.querySelectorAll(".MathJax mtext").forEach((element) => {
        element.style.direction = "rtl !important";
        element.style.textAlign = "right";
        element.style.whiteSpace = "nowrap";
    });
});
</script> --}}

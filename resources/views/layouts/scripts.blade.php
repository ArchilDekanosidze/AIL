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
</script>
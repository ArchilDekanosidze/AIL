@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/admin/question/index.css')}}">
@endsection
@section('content')
<p class="breadcrump"> @php echo $path @endphp </p>
<div class="adminCategory main-body">
    <div class="mainDivDirection">
        {{$i = 1;}}
        @foreach($questions as $question)
            <div class="questioCard">
                <div>سوال شماره : {{$i++}} ({{$question->id}})({{ $question->percentage }})<input class="questionId" type="hidden" value="{{$question->id}}"> <button class="changeToSimple">تغییر به ساده</button> </div>
                <div class="questionFront">{!! $question->front !!}</div>
                <div class="questionFront">{!! $question->p1 !!}</div>
                <div class="questionFront">{!! $question->p2 !!}</div>
                <div class="questionFront">{!! $question->p3 !!}</div>
                <div class="questionFront">{!! $question->p4 !!}</div>
                <div class="questionFront">{!! $question->back !!}</div>
                <div class="cardButtons">
                    @if($question->type == 'test')                    
                        <a class="editLink" href="{{route('admin.question.edit', $question->id)}}">ادیت</a>                    
                    @else                    
                        <a class="editLink" href="{{route('admin.question.descriptive.edit', $question->id)}}">ادیت</a>
                    @endif
                    <a class="deleteLink" href="{{route('admin.question.delete', $question->id)}}">حذف</a>                        
                    <a class="showLink" href="{{route('admin.question.show', $question->id)}}">نمایش</a>                        
                </div> 
                  
            </div>         
        @endforeach                
    </div>
</div>
@endsection



@section('scripts')
<script>
    $(document).ready(function() {
        $(".changeToSimple").click(function() {            
              var url = "{{route('admin.question.updatelevel.post')}}";    
              questionId =     $(this).parent().find('.questionId').val()
              data =  { 
                  questionId:  questionId,
                } ;
                
                result = Ajax(url, data)  
                console.log(result)
                 if(result == "success") {
                $(".success-message").html("درصد با موفقیت تغییر کرد");   
                $('.success-message').show().delay(1000).fadeOut('slow');
                 }
        })  
    });
</script>
@endsection

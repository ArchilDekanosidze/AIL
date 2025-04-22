<li style="margin-right: {{$category->depth *20}}px;">
    {{$category->name}}
</li>
@if($category->children->isNotEmpty())  
    <ul>
        @foreach($category->children as $child)
            @include('partials.question.subcategories', ['category', $child]);
        @endforeach
    </ul>
@endif
@foreach($books as $book)
    <td  style="border:1px solid #ddd; text-align:center; padding:5px; box-shadow:0 1px 7px -3px rgba(0,0,0,0.1); transition:all 0.3s; background:#fff;">
        <div class="views-field views-field-field-book-image">
            <div class="field-content">
                <a href="{{ url('/books/' . $book->id) }}">
                    <img src="{{ $book->image }}" alt="" width="152" height="220">
                </a>
            </div>
        </div>
        <div class="views-field views-field-title">
            <span class="field-content">
                <a href="{{ url('/books/' . $book->id) }}">{{ $book->title }}</a>
            </span>
        </div>
        <span class="views-field views-field-field-book-code">
            <span class="views-label views-label-field-book-code">کد کتاب: </span>
            <span class="field-content">{{ $book->code }}</span>
        </span>
    </td>
@endforeach

@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{asset('assets/css/category/categoryBook/index.css')}}">
@endsection
@section('content')
<div class="CategoryBook main-body">    
    <div class="mainDivDirection">
        <div id="category-selectors">
            @for ($i = 0; $i < 6; $i++)
                <select class="category-select" data-level="{{ $i }}" style="{{ $i === 0 ? '' : 'display: none;' }}"></select>
            @endfor
            @php
                $startYear = 1381;
                $endYear = 1403;
            @endphp

            <select id="year-select" name="field_year_tid" class="form-select">
                <option value="">- همه سال‌ها -</option>
                @for ($year = $endYear; $year >= $startYear; $year--)
                        <option value="{{ $year }}-{{ $year + 1 }}">{{ $year }}-{{ $year + 1 }}</option>
                @endfor
            </select>
            <button id="search-button" class="btn btn-primary">جست‌وجو</button>
        </div>

        <div id="search-result" class="row flex-wrap"></div>
        <div id="pagination-wrapper" class="mt-4 text-center"></div>
    </div>
</div>
@endsection







@section('scripts')
    <script>
             
        $(document).ready(function() {
            const selectors = $('.category-select');
            // Load top-level categories (parent_id = 0)
            loadCategories(1, selectors.eq(0));

            selectors.on('change', function () {
                const level = parseInt($(this).data('level'));
                const selectedId = $(this).val();

                // Hide and reset all lower levels
                selectors.slice(level + 1).each(function () {
                    $(this).hide().empty();
                });

                if (selectedId) {
                    // Load next level categories
                    $.get('/categories/categoryBook/children/' + selectedId, function (data) {
                        if (data.length > 0 && level + 1 < selectors.length) {
                            const nextSelect = selectors.eq(level + 1);
                            nextSelect.append('<option value="">انتخاب کنید</option>');
                            $.each(data, function (i, item) {
                                nextSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                            });
                            nextSelect.show();
                        }
                    });
                }
            });

            function loadCategories(parentId, $select) {
                $.get('/categories/categoryBook/children/' + parentId, function (data) {
                    $select.append('<option value="">انتخاب کنید</option>');
                    $.each(data, function (i, item) {
                        $select.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                    $select.show();
                });
            }



        $('#search-button').on('click', function () {
            fetchBooks(1); // Start from page 1
        });

        function fetchBooks(page = 1) {
            const year = $('#year-select').val();
            let selectedCategoryId = null;

            $('.category-select').each(function () {
                const val = $(this).val();
                if (val) {
                    selectedCategoryId = val;
                }
            });

            const url = "{{ route('category.categoryBook.getBooks') }}" + "?page=" + page;

            const result = Ajax(url, {
                year: year || null,
                category_id: selectedCategoryId || null,
            });

            $('#search-result').html(result.html);
            $('#pagination-wrapper').html(result.pagination);
        }

        // Handle pagination clicks
       // Handle pagination links
        $(document).on('click', '#pagination-wrapper a', function (e) {
            e.preventDefault();

            const url = $(this).attr('href');

            // Extract query string (e.g., ?page=2)
            const queryString = url.split('?')[1];

            // Get current filters
            const year = $('#year-select').val();
            let selectedCategoryId = null;
            $('.category-select').each(function () {
                const val = $(this).val();
                if (val) {
                    selectedCategoryId = val;
                }
            });

            // Build data object
            let data = {
                year: year || null,
                category_id: selectedCategoryId || null,
            };

            if (queryString) {
                const params = new URLSearchParams(queryString);
                data.page = params.get("page");
            }

            // Use your Ajax() function
            const result = Ajax("{{ route('category.categoryBook.getBooks') }}", data, "GET");

            // Render response
            $('#search-result').html(result.html);
            $('#pagination-wrapper').html(result.pagination);
        });


            $('#search-button').click();







            


        });
    </script>
@endsection
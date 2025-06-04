@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryGambeGam/index.css') }}">
@endsection

@section('content')
<div class="CategoryBook main-body">     
    <div class="mainDivDirection">
        <div id="category-selectors" class="mb-3">
            @for ($i = 0; $i < 6; $i++)
                <select class="category-select form-select mb-2" data-level="{{ $i }}" style="{{ $i === 0 ? '' : 'display: none;' }}"></select>
            @endfor

            <button id="search-button" class="btn btn-primary mt-2">جست‌وجو</button>
        </div>

        <div id="search-result" class="row flex-wrap"></div>
        <div id="pagination-wrapper" class="mt-4 text-center"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        const selectors = $('.category-select');

        // Load top-level categories
        loadCategories(1, selectors.eq(0));

        // On category change
        selectors.on('change', function () {
            const level = parseInt($(this).data('level'));
            const selectedId = $(this).val();

            // Reset and hide lower levels
            selectors.slice(level + 1).each(function () {
                $(this).hide().empty();
            });

            if (selectedId) {
                $.get('/categories/categoryGambeGam/children/' + selectedId, function (data) {
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
            $.get('/categories/categoryGambeGam/children/' + parentId, function (data) {
                $select.append('<option value="">انتخاب کنید</option>');
                $.each(data, function (i, item) {
                    $select.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
                $select.show();
            });
        }

        $('#search-button').on('click', function () {
            fetchBooks(1);
        });

        function fetchBooks(page = 1) {
            let selectedCategoryId = null;

            $('.category-select').each(function () {
                const val = $(this).val();
                if (val) selectedCategoryId = val;
            });

            const url = "{{ route('category.categoryGambeGam.getGambeGams') }}" + "?page=" + page;

            const result = Ajax(url, {
                category_id: selectedCategoryId || null
            });

            $('#search-result').html(result.html);
            $('#pagination-wrapper').html(result.pagination);
        }

        // Handle pagination
        $(document).on('click', '#pagination-wrapper a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const queryString = url.split('?')[1];
            let selectedCategoryId = null;

            $('.category-select').each(function () {
                const val = $(this).val();
                if (val) selectedCategoryId = val;
            });

            let data = { category_id: selectedCategoryId || null };

            if (queryString) {
                const params = new URLSearchParams(queryString);
                data.page = params.get("page");
            }

            const result = Ajax("{{ route('category.categoryGambeGam.getGambeGams') }}", data, "GET");

            $('#search-result').html(result.html);
            $('#pagination-wrapper').html(result.pagination);
        });

        // Load initial data
        $('#search-button').click();
    });
</script>
@endsection

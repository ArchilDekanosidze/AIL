@extends('layouts.master')

@section('style')
<link rel="stylesheet" href="{{ asset('assets/css/category/categoryJozve/index.css') }}">
@endsection

@section('content') 
@if ($errors->has('category_jozve_id'))
    <span class="text-danger">{{ $errors->first('category_jozve_id') }}</span>
@endif

<div class="CategoryBook main-body">     
    <div class="mainDivDirection">
        <div class="input-group mb-3">
            <input type="text" id="search-text" class="form-control" placeholder="جستجو در عنوان یا توضیحات">
        </div>
        <div id="category-selectors" class="mb-3">
            @for ($i = 0; $i < 6; $i++)
                <select class="category-select form-select mb-2" data-level="{{ $i }}" style="{{ $i === 0 ? '' : 'display: none;' }}"></select>
            @endfor

            <button id="search-button" class="btn btn-primary mt-2">جست‌وجو</button>
        </div>

        <div id="search-result" class="row flex-wrap"></div>
        <div id="pagination-wrapper" class="mt-4 text-center"></div>
        <div class="container" style="margin-top: 40px;">
            <h3 class="text-center">افزودن جزوه جدید</h3>

            @auth
                <form action="{{ route('jozves.store') }}" method="POST" enctype="multipart/form-data" class="sp-form">
                    @csrf

                    <div class="form-group">
                        <label for="title">عنوان جزوه:</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="description">توضیحات:</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="file_path">فایل جزوه:</label>
                        <input type="file" name="file_path" id="file_path" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" required>
                    </div>

                    <input type="hidden" name="category_jozve_id" id="category_jozve-id" value="">


                    <button type="submit" class="btn btn-success mt-3">ارسال جزوه</button>
                </form>
            @endauth

            @guest
                <div class="alert alert-warning text-center mt-4">
                    برای افزودن جزوه جدید، ابتدا وارد حساب کاربری خود شوید.
                    <br>
                    <a href="{{ route('auth.login.form') }}" class="btn btn-primary mt-2">ورود به حساب کاربری</a>
                </div>
            @endguest
        </div>


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
                $.get('/categories/categoryJozve/children/' + selectedId, function (data) {
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
            $.get('/categories/categoryJozve/children/' + parentId, function (data) {
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

            const searchText = $('#search-text').val();

            const url = "{{ route('category.categoryJozve.getJozve') }}" + "?page=" + page;

            const result = Ajax(url, {
                category_id: selectedCategoryId || null,
                search: searchText || null
            });

            $('#search-result').html(result.html);
            $('#pagination-wrapper').html(result.pagination);
            updatePagination();
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

            const result = Ajax("{{ route('category.categoryJozve.getJozve') }}", data, "GET");

            $('#search-result').html(result.html);
            $('#pagination-wrapper').html(result.pagination);
            updatePagination();
        });

        // Load initial data
        $('#search-button').click();
    });

    document.addEventListener('change', function (e) {
        if (e.target.matches('.category-select')) {
            updateCategoryJozveId();
        }
    });

    function updateCategoryJozveId() {
        const selects = document.querySelectorAll('.category-select');
        let lastSelectedValue = '';

        selects.forEach(select => {
            if (select.value) {
                lastSelectedValue = select.value;
            }
        });

        document.getElementById('category_jozve-id').value = lastSelectedValue;
    }
    document.querySelector('form').addEventListener('submit', function (e) {
        const categoryId = document.getElementById('category_jozve-id').value;

        if (!categoryId) {
            e.preventDefault();
            alert('لطفاً یک دسته‌بندی انتخاب کنید.');
        }
    });

</script>
@endsection

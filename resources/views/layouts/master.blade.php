<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-tag')
    @yield('head-tag')
    @yield('style')
</head>

<body dir="rtl">
    @include('layouts.header')

    <section>
        <section>
            @yield('content')
        </section>
    </section>
    @include('layouts.scripts')
    @yield('scripts')
</body>

</html>
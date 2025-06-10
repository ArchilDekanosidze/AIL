<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.head-tag')
    @yield('head-tag')
    @yield('style')
</head>

<body dir="rtl">
    @include('layouts.header')
    
    <section>
        <section>

            <div class="messages">
                <div class="success-message">
                </div>
                <div class="failed-message">
                </div>
                @if($errors->any())
                    <h4 class="errorFromController">{{$errors->first()}}</h4>
                @endif
                @isset($errorMessages)
                    @foreach ($errorMessages as $key => $value)
                        <h4 class="errorFromController">{{$value}}</h4>
                    @endforeach
                @endisset

                @isset($successMessages)
                    @foreach ($successMessages as $key => $value)
                        <h4 class="successFromController">{{$value}}</h4>
                    @endforeach
                @endisset
                @if(session()->has('success'))
                    <h4 class="successFromController">{{session('success')}}</h4>
                @endif
                
            </div>
            @yield('content')
        </section>
    </section>
    @include('layouts.scripts')
    @yield('scripts')
    @yield('scripts2')
</body>

</html>
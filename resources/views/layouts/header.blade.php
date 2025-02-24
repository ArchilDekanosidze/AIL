<div class="mainHeader">
    @auth
        @if (Request::is("/"))
            <a href="{{route('desktop.student.index')}}" class="btn btn-primary">پنل کاربری</a>
        @else
            <a href="{{route('home')}}" class="btn btn-primary">خانه</a>
        @endif
        <a href="{{route('auth.logout')}}" class="btn btn-primary">خروج</a>
    @endauth
    @guest
        <a href="{{route('auth.login.form')}}" class="btn btn-primary">ورود</a>        
    @endguest
</div>
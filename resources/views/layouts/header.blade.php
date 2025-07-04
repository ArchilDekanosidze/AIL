<div class="mainHeader">
    @auth
        @if (Request::is("/"))
            <a href="{{route('desktop.student.index')}}" class="btn btn-primary">پنل کاربری</a>
            <a href="{{route('profile.student.index', Auth::user()->id)}}" class="btn btn-primary">پروفایل</a>
        @else
            <a href="{{route('home')}}" class="btn btn-primary">خانه</a>
        @endif
        <a href="{{route('auth.logout')}}" class="btn btn-primary">خروج</a>
    @endauth
    @guest
        <a href="{{route('auth.login.form')}}" class="btn btn-primary">ورود</a>        
    @endguest
</div>
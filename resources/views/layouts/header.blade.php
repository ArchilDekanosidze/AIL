<div class="mainHeader">
    @auth
        <a href="{{route('desktop.student.index')}}" class="btn btn-primary">پنل کاربری</a>
        <a href="{{route('auth.logout')}}" class="btn btn-primary">خروج</a>
    @endauth
    @guest
        <a href="{{route('auth.login.form')}}" class="btn btn-primary">ورود</a>        
    @endguest
</div>
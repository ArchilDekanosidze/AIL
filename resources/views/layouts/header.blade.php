<div class="mainHeader">
    @auth
        <a href="{{route('desktop.student.index')}}" class="btn btn-primary">پنل کاربری</a>
    @endauth
    @guest
        <a href="{{route('auth.login.form')}}" class="btn btn-primary">ورود</a>
    @endguest
</div>
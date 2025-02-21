<div class="mainHeader">
    @auth
        <a href="{{route('desktop.index')}}" class="btn btn-primary">پنل کاربری</a>
    @endauth
    @guest
        <a  class="btn btn-primary">ورود</a>
    @endguest
</div>
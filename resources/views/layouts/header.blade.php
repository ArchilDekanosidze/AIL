<div class="mainHeader">
    @auth
        <a href="{{route('profile.index')}}" class="btn btn-primary">پنل کاربری</a>
    @endauth
    @guest
        <a  class="btn btn-primary">ورود</a>
    @endguest
</div>
<div class="header navbar navbar-expand">
    <ul class="navbar-nav">
        <li>
            <a id="sidebar-toggle" class="sidebar-toggle" href="javascript:void(0);">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">

            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{Auth::user()->name}}</a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="/user/settings">Настройки</a>
                <a class="dropdown-item" href="/auth/logout">Выйти</a>
            </div>

        </li>
    </ul>
</div>
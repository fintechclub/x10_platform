<nav class="navbar navbar-expand-lg d-md-none d-lg-none navbar-dark bg-dark mobile-navbar">
    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01"
            aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>
    <div class="logo"></div>
    <i class="user-photo"></i>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item">
                <a href="/user/settings" class="nav-link"><i class="fas fa-cog"></i> Настройки</a>
            </li>
            <li class="nav-item {{Request::is('dashboard') ? 'active' : ''}}">
                <a href="/dashboard" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="fas fa-chart-bar"></i> Portfolio</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="fas fa-question"></i> FAQ</a>
            </li>
        </ul>
    </div>
</nav>

<div class="header navbar navbar-expand d-none d-sm-none d-md-flex d-lg-flex">
    <ul class="navbar-nav">
        <li>
            <a id="sidebar-toggle" class="sidebar-toggle" href="javascript:void(0);">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">

            <a href="#" class="nav-link dropdown-toggle username" data-toggle="dropdown">
                {{Auth::user()->name}}
                <i class="user-photo"></i>
            </a>


            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="/user/settings">Настройки</a>
                <a class="dropdown-item" href="/auth/logout">Выйти</a>
            </div>

        </li>
    </ul>
</div>
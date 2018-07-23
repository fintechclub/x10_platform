<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/styles.css?v={{env('APP_VER',time())}}"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&amp;subset=cyrillic"
          rel="stylesheet">

    {!! SEO::generate(true) !!}

    {{--@todo: remove debug classes--}}
    <style>
        .trashed{
            text-decoration: line-through;
            opacity: 0.4;
        }
    </style>

</head>
<body>

<div>
    <div class="sidebar d-none d-sm-none d-md-block d-lg-block">
        <a href="/" class="logo">
        </a>

        <ul class="list-unstyled menu">
            <li class="{{Request::is('dashboard') || Request::is('dashboard/*') ? 'active' : ''}}">
                <a href="/dashboard"><i class="fas fa-home"></i> <span class="t-hide">Dashboard</span></a>
            </li>
            <li class="{{Request::is('portfolio') || Request::is('portfolio/*') ? 'active' : ''}}">
                <a href="/portfolio"><i class="fas fa-chart-bar"></i> <span class="t-hide">Portfolio</span></a>
            </li>
            <li class="{{Request::is('faq') || Request::is('faq/*') ? 'active' : ''}}">
                <a href="/faq">
                    <i class="fas fa-question"></i> <span class="t-hide">FAQ</span></a>
            </li>
        </ul>
    </div>
    <div class="page-container">
        @include('navbar')
        <div class="main-content mt-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
<script src="/js/notify.min.js"></script>

@yield('scripts')

<script>
    $(function () {

        if (Cookies.get('toggled') === 'true') {
            $('.sidebar').addClass('toggled');
            $('.page-container').addClass('main-toggled');
        }

        $('.sidebar-toggle').click(function () {
            $('.sidebar').toggleClass('toggled');
            $('.page-container').toggleClass('main-toggled');
            if ($('.sidebar').hasClass('toggled')) {
                Cookies.set('toggled', 'true');
            } else {
                Cookies.set('toggled', 'false');
            }
        });
    })
</script>
</body>
</html>
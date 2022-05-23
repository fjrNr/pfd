<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Post Flight Document</title>
    <link rel="shortcut icon" href="{{ asset('public/flight.png') }}">

    <!-- Styles -->
    <link href="{{ asset('public/css/menu/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/menu/sb-admin.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/font-awesome.v4.7.0.css') }}" rel="stylesheet">    
    @yield('styles')

    <!-- Scripts -->
    <script src="{{ asset('public/js/jquery-2.0.3.min.js') }}"></script>
    <script src="{{ asset('public/js/jquery-ui.custom.v1.12.1.min.js') }}"></script>
    <script src="{{ asset('public/js/menu/bootstrap.v4.0.0.min.js')}}"></script>
    <script src="{{ asset('public/js/menu/sb-admin.min.js')}}"></script>
    @yield('scripts')

</head>

<style type="text/css">
    .a-tag-disabled{
        pointer-events : none;
        cursor: not-allowed;
    }
</style>

<body class="fixed-nav sticky-footer bg-light" id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" id="mainNav">
        <a class="navbar-brand" href="{{url('/home') }}" style="font-weight: bold;">Post Flight Document</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link" href="{{url('/') }}">
                        <i class="fa fa-fw fa-dashboard"></i>
                        <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>

                @if(Auth::user()->group == 'ADMIN')
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseMaster" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-database"></i>
                        <span class="nav-link-text">Master Data</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseMaster">
                        <li>
                            <a href="{{url('/accounts/index') }}">Account</a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'SUBM OFC')
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseReceiving" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-file"></i>
                        <span class="nav-link-text">Receiving</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseReceiving">
                        <li>
                            <a href="{{url('/submission/entry') }}">Entry Submission</a>
                        </li>
                        <li>
                            <a href="{{url('/submission/index') }}">View Received Submissions</a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC')
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseArchive" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-file"></i>
                        <span class="nav-link-text">Archiving</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseArchive">
                        <li>
                            <a href="{{url('/box/entry') }}">Entry Submissions Box</a>
                        </li>
                        <li>
                            <a href="{{url('/box/entry/afl') }}">Entry AFLs Box</a>
                        </li>
                        <li>
                            <a href="{{url('/box/index') }}">View Submissions Boxes</a>
                        </li>
                        <li>
                            <a href="{{url('/box/index/afl') }}">View AFL Boxes</a>
                        </li>
                    </ul>
                </li>
                @endif

                @if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST')

                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseMovement" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-file"></i>
                        <span class="nav-link-text">Movement</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseMovement">
                        <li>
                            <a href="{{url('/movement/entry') }}">Entry Movement Request</a>
                        </li>
                        <li>
                            <a href="{{url('/movement/index') }}">View Requested Movements</a>
                        </li>
                    </ul>
                </li>
                @endif
                
                @if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC' || Auth::user()->group == 'ANALYST')
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link" href="{{url('/tracking') }}">
                        <i class="fa fa-fw fa-search"></i>
                        <span class="nav-link-text">Tracking</span>
                    </a>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#exampleModal"><?php date_default_timezone_set("Asia/Jakarta"); echo date("l, d-m-Y"); ?></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="My Account: {{Auth::user()->name}}">
                        {{Auth::user()->name}}, {{Auth::user()->group}}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
                        <a class="dropdown-item" href="{{ url('/user') }}">
                            <span>
                                <strong>My Profile</strong>
                            </span>
                        </a>
                        <a class="dropdown-item" href="{{ url('/user/password') }}">
                            <span>
                                <strong>Change Password</strong>
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="text-danger">
                                <strong>Logout</strong>
                            </span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content-wrapper">
        @yield('content')
        <br>
    </div>
</body>

</html>

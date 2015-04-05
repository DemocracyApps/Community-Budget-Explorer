<!doctype html>
<!--
--
--  This file is part of the Government Budget Explorer (GBE).
--
--  The GBE is free software: you can redistribute it and/or modify
--  it under the terms of the GNU General Public License as published by
--  the Free Software Foundation, either version 3 of the License, or
--  (at your option) any later version.`
--
--  The GBE is distributed in the hope that it will be useful,
--  but WITHOUT ANY WARRANTY; without even the implied warranty of
--  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
--  GNU General Public License for more details.
--
--  You should have received a copy of the GNU General Public License
--  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
<head>
    <meta charset="utf-8">
    <title>Government Budget Explorer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/local.css">
    <!-- STYLES TO MAKE IT NICER LOOKING -->
    <style>
        h1, h2, h3, h4, h5, h6{
            font-family: 'Titillium Web' !important;
        }
        h1, h2, h3{
            font-weight: 300;
        }
        a{
            color: #EB7722;
        }
        ul, p, li, th, td, input, a {
            font-size: 110%;
        }
        div.presentation {
            font-size: 110%;
        }
    </style>
    @yield('head')
    <!-- END STYLES TO MAKE IT NICER LOOKING -->
</head>
  <?php
          use DemocracyApps\GB\Helpers as Helpers;
  ?>

<body>
@include('templates.datarider')
<div class="cnp-header">
    <div class="container">
        <div class="row">
            <div class="col-md-1 hdr-logo">
                <img src="/img/DemocracyApps_logo-01_RGB.jpg" height="123" width="96" alt="DemocracyApps Logo"/>
            </div>
            <div class="col-md-8 hdr-main">
                <h1>Government Budget Explorer</h1>
            </div>
            <div class="col-md-3 hdr-right">
                <ul class="nav nav-pills">
                    <li role="presentation"><a href="/">Home</a></li>
                    @if (Auth::guest())
                        <li role="presentation" ><a href="/auth/login">Log In</a></li>
                    @else
                        <li role = "presentation" class="dropdown">
                            <a class="dropdown-Toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                                Admin <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li role="presentation" ><a href="/user/profile">My Stuff</a></li>
                                <?php
                                $user = Auth::user();
                                $governmentOrg = $user->getGovernmentOrg();
                                $mediaOrg = $user->getMediaOrg();
                                ?>
                                @if ($governmentOrg != null)
                                    <li role="presentation"><a href="/governments/{!!$governmentOrg->id!!}">{!!$governmentOrg->name!!}</a></li>
                                @endif
                                @if ($mediaOrg != null)
                                    <li role="presentation"><a href="/media/{!!$mediaOrg->id!!}">{!! $mediaOrg->name !!}</a></li>
                                @endif
                                @if ($user->projectcreator)
                                    <li role="presentation" ><a href="/admin/projects">Projects</a></li>
                                @endif
                                @if ($user->superuser)
                                    <li role="presentation" ><a href="/system/settings">System</a></li>
                                @endif
                                <li role="presentation" ><a href="/auth/logout">Log Out</a></li>

                                <li role="presentation" class="disabled" style="display:none;"><a href="#">Disabled link</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-7">
            <h1>
                @yield('title')
            </h1>
        </div>
        <div class="col-sm-5" style="float:right;">
            <div style="float:right;">
                @yield('buttons')
            </div>
        </div>
    </div>
</div>

<div class="container app-container">
    <div class="row">
        <div class="col-md-6">
            <span id="flash" class="error text-danger bg-danger">{!! Session::get('gbe_error') !!} </span>
        </div>
    </div>

    @yield('content')
</div>
<br>
<footer class="row">
    <div class="col-md-1">
    </div>
    <div class="col-md-5">
        <div id="copyright">Copyright 2015 DemocracyApps</div>
    </div>
    <div class="col-md-5" style="text-align:right;">
        @yield('footer_right')
    </div>
    <div class="col-md-1">
    </div>
</footer>
<br>
<script src="/js/jquery-2.1.1.min.js"></script>
<script src="/js/jquery-ui-1.11.1/jquery-ui.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.cookie.js"></script>

@yield('scripts')
</body>

</html>


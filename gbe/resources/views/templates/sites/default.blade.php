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
        <link rel="stylesheet" type="text/css" href="/css/all.css">

        @yield('head')
    </head>

    <?php
    use DemocracyApps\GB\Helpers as Helpers;
    ?>

    <body>
        @include('templates.datarider')

        @yield('content')

        <script src="/js/all.js"></script>
        <script src="/js/app.js"></script>

        @yield('scripts')
    </body>

</html>

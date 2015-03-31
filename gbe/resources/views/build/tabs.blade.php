<ul class="nav nav-tabs">
    <li role="presentation" @if ($page == 'settings') class="active" @endif ><a href="/build/{!! $site->slug !!}">Site Settings</a></li>
    <li role="presentation" @if ($page == 'pages') class="active" @endif ><a href="/build/{!! $site->slug !!}/pages">Pages</a></li>
    <li role="presentation" @if ($page == 'cards') class="active" @endif ><a href="/build/{!! $site->slug !!}/content">Cards</a></li>
</ul>
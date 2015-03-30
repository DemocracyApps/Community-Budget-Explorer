<ul class="nav nav-tabs">
    <li role="presentation" @if ($page == 'organization') class="active" @endif ><a href="/media/{!! $organization->id !!}">Organization</a></li>
    <li role="presentation" @if ($page == 'sites') class="active" @endif ><a href="/media/{!! $organization->id !!}/sites">Sites</a></li>
    <li role="presentation" @if ($page == 'users') class="active" @endif ><a href="/media/{!! $organization->id !!}/users">Users</a></li>
</ul>
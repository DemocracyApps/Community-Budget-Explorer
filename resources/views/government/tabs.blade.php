<ul class="nav nav-tabs">
    <li role="presentation" @if ($page == 'organization') class="active" @endif ><a href="/governments/{!! $organization->id !!}">Organization</a></li>
    <li role="presentation" @if ($page == 'data') class="active" @endif ><a href="/governments/{!! $organization->id !!}/data">Data</a></li>
    <li role="presentation" @if ($page == 'sites') class="active" @endif ><a href="/governments/{!! $organization->id !!}/sites">Sites</a></li>
    <li role="presentation" @if ($page == 'users') class="active" @endif ><a href="/governments/{!! $organization->id !!}/users">Users</a></li>
</ul>
<ul class="nav nav-tabs">
    <li role="presentation" @if ($page == 'settings') class="active" @endif ><a href="/system/settings">Settings</a></li>
    <li role="presentation" @if ($page == 'users') class="active" @endif ><a href="/system/users">Users</a></li>
    <li role="presentation" @if ($page == 'governments') class="active" @endif ><a href="/system/governments">Governments</a></li>
    <li role="presentation" @if ($page == 'media') class="active" @endif ><a href="/system/media">Media</a></li>
    <li role="presentation" @if ($page == 'layouts') class="active" @endif ><a href="/system/layouts">Layouts</a></li>
</ul>


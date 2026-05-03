<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        {{-- Dashboard --}}
        @can('view dashboard')
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        @endcan

        {{-- RA Dashboard --}}
        @can('view ra-dashboard')
        <li class="nav-item">
            <a href="{{ route('ra.dashboard') }}" class="nav-link {{ Request::routeIs('ra.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-gavel"></i>
                <p>Challenge Mechanism Portal</p>
            </a>
        </li>
        @endcan

        {{-- Auction Management --}}
        @canany(['view auctions', 'view npv-categories'])
        <li class="nav-header text-uppercase" style="font-size:10px; letter-spacing:1px; opacity:.6;">Auction Management</li>
        @endcanany

        @can('view npv-categories')
        <li class="nav-item {{ Request::routeIs('npv-categories.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Request::routeIs('npv-categories.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tags"></i>
                <p>NPV Categories <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('npv-categories.index') }}" class="nav-link {{ Request::routeIs('npv-categories.index') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>All Categories</p>
                    </a>
                </li>
                @can('create npv-categories')
                <li class="nav-item">
                    <a href="{{ route('npv-categories.create') }}" class="nav-link {{ Request::routeIs('npv-categories.create') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>Add Category</p>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('view auctions')
        <li class="nav-item {{ Request::routeIs('auctions.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Request::routeIs('auctions.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-gavel"></i>
                <p>Auctions <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('auctions.index') }}" class="nav-link {{ Request::routeIs('auctions.index') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>All Auctions</p>
                    </a>
                </li>
                @can('create auctions')
                <li class="nav-item">
                    <a href="{{ route('auctions.create') }}" class="nav-link {{ Request::routeIs('auctions.create') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>Add Auction</p>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        {{-- User Management --}}
        @canany(['view users', 'view roles'])
        <li class="nav-header text-uppercase" style="font-size:10px; letter-spacing:1px; opacity:.6;">User Management</li>
        @endcanany

        @can('view users')
        <li class="nav-item {{ Request::routeIs('users.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Users <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>All Users</p>
                    </a>
                </li>
                @can('create users')
                <li class="nav-item">
                    <a href="{{ route('users.create') }}" class="nav-link {{ Request::routeIs('users.create') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>Add User</p>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('view roles')
        <li class="nav-item {{ Request::routeIs('roles.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Request::routeIs('roles.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tag"></i>
                <p>Roles <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link {{ Request::routeIs('roles.index') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>All Roles</p>
                    </a>
                </li>
                @can('create roles')
                <li class="nav-item">
                    <a href="{{ route('roles.create') }}" class="nav-link {{ Request::routeIs('roles.create') ? 'active' : '' }}">
                        <i class="far fa-dot-circle nav-icon"></i><p>Add Role</p>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        {{-- Account --}}
        <li class="nav-header text-uppercase" style="font-size:10px; letter-spacing:1px; opacity:.6;">Account</li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>

    </ul>
</nav>

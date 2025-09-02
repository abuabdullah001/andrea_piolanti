<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row align-items-center">
            <li class="nav-item mr-auto">
                <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
                    <img src="{{ asset($setting->admin_logo ?? 'backend/app-assets/images/logo/logo.png') }}"
                        width="40" alt="logo">
                    <h2 class="brand-text">{{ $setting->admin_title ?? 'My Admin' }}</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
                    <i data-feather="home"></i>
                    <span class="menu-item text-truncate" data-i18n="Analytics">
                        Home
                    </span>
                </a>
            </li>

            <li class="navigation-header">
                <span data-i18n="Charts &amp; Maps">
                    Master Data
                </span>
                <i data-feather="more-horizontal"></i>
            </li>

            <li class=" nav-item {{ request()->routeIs('category.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('category.index') }}">
                    <i data-feather="grid"></i>
                    <span class="menu-item text-truncate" data-i18n="Analytics">
                        Category
                    </span>
                </a>
            </li>

            {{-- <li class=" nav-item {{ request()->routeIs('promo.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('promo.index') }}">
                    <i data-feather="tag"></i>
                    <span class="menu-item text-truncate" data-i18n="Analytics">
                        Promo Code
                    </span>
                </a>
            </li> --}}

            <li class="navigation-header">
                <span data-i18n="Charts &amp; Maps">
                    Contents
                </span>
                <i data-feather="more-horizontal"></i>
            </li>

            <li class=" nav-item {{ request()->routeIs('businessowners.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('businessowners.index') }}">
                    <i data-feather="shopping-bag"></i>
                    <span class="menu-item text-truncate" data-i18n="Analytics">
                        Business Owner's
                    </span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('booking.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('booking.index') }}">
                    <i data-feather="bookmark"></i>
                    <span class="menu-item text-truncate" data-i18n="Analytics">
                        Bookings
                    </span>
                </a>
            </li>

            <li class="navigation-header">
                <span data-i18n="Charts &amp; Maps">
                    Subscriptions
                </span>
                <i data-feather="more-horizontal"></i>
            </li>


            <li class="nav-item {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="bell"></i>
                    <span class="menu-title text-truncate" data-i18n="Charts">
                        Subscription Plans
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ request()->routeIs('subscription.plans', 'subscription.edit') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('subscription.plans') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate">
                                Plan List
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('subscription.activeUsers') ? 'active' : '' }}">
                        <a href="{{ route('subscription.activeUsers') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate">
                                Active Users
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('subscription.freeTrial') ? 'active' : '' }}">
                        <a href="{{ route('subscription.freeTrial') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate">
                                Free Trial Users
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('subscription.expiredUsers') ? 'active' : '' }}">
                        <a href="{{ route('subscription.expiredUsers') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate">
                                Expired Users
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class=" navigation-header">
                <span data-i18n="Charts &amp; Maps">
                    Settings
                </span>
                <i data-feather="more-horizontal"></i>
            </li>

            <li
                class="nav-item {{ request()->routeIs(['admin.setting.*', 'profile.*']) ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="settings"></i>
                    <span class="menu-title text-truncate" data-i18n="Charts">
                        System Settings
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('profile') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Profile Settings
                            </span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('system.setting') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('system.setting') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                System Setting
                            </span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.setting') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.setting') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Admin Setting
                            </span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.setting.mail') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.setting.mail') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Mail Setting
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- <li class="nav-item {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.role.list') }}">
                    <i data-feather="shield"></i>
                    <span class="menu-title text-truncate">
                        Role Management
                    </span>
                </a>
            </li> --}}

            <li class="nav-item {{ request()->routeIs('user.list') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="ag-grid">
                        Users
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('user.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('user.create') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Create User
                            </span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('user.list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('user.list') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                User List
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

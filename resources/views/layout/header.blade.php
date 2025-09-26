<header class="header-customer">
    <div class="logo"><a href="{{ route('home') }}">JBADMINTON</a></div>
    <nav>
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fa-solid fa-house-chimney"></i> Trang chủ
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('booking.*') ? 'active' : '' }}">
                <a href="{{ route('booking.index') }}" class="nav-link">
                    <i class="fa-solid fa-calendar-alt"></i> Đặt lịch
                </a>
            </li>
            @auth
                @if (Auth::user()->role == 'customer')
                    <li class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                        <a href="{{ route('profile') }}" class="nav-link">
                            <i class="fas fa-user-circle input-icon"></i> Tài khoản
                        </a>
                    </li>
                @elseif(Auth::user()->role == 'owner')
                    <li class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                        <a href="{{ route('profile') }}" class="nav-link">
                            <i class="fas fa-user-circle input-icon"></i> Tài khoản
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('courts*') || request()->is('owner*') ? 'active' : '' }}">
                        <a href="{{ route('owner.bookings.index') }}" class="nav-link">
                            <i class="fa-solid fa-chart-line"></i> Trang quản lý
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <a href="" class="nav-link">
                            <button type="submit">
                                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                            </button>
                        </a>
                    </form>
                </li>
            @else
                <li class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng nhập
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('register') ? 'active' : '' }}">
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="fa-regular fa-registered"></i> Đăng ký
                    </a>
                </li>
            @endauth
            <div class="line"></div>
        </ul>
    </nav>
</header>

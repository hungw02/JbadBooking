<section class="sidebar-container">
    <div class="sidebar">
        <ul>
            <li class="sidebar-item active" onclick="setActive(this)">
                <div class="icon"><x-radix-calendar /></div>
                <a href="{{ route('booking.index')}}" class="text">Đặt lịch</a>
            </li>
            <li class="sidebar-item" onclick="setActive(this)">
                <div class="icon"><x-radix-id-card /></div>
                <a href="{{ route('booking.list')}}" class="text">Quản lý lịch đặt</a>
            </li>
            <li class="sidebar-item" onclick="setActive(this)">
                <div class="icon"><x-radix-person /></div>
                <a href="{{ route('teammate.index')}}" class="text">Tìm đồng đội</a>
            </li>
        </ul>
    </div>
</section>
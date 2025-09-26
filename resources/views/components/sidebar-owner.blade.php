<session class="fixed top-0 left-0 h-full z-40 flex">
    <!-- Sidebar -->
    <div id="sidebar-owner" class="bg-gray-900 h-full transition-all duration-300 ease-in-out w-16 hover:w-64 pt-20 overflow-hidden">
        <div class="flex flex-col h-full">
            <!-- Menu Items -->
            <a href="{{ route('owner.bookings.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-calendar-check text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý đơn đặt</span>
                </div>
            </a>

            <a href="{{ route('customers.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-users text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý khách hàng</span>
                </div>
            </a>

            <a href="{{ route('products.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-box text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý sản phẩm</span>
                </div>
            </a>

            <a href="{{ route('storage.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-exchange-alt text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý cửa hàng</span>
                </div>
            </a>

            <a href="{{ route('imports.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-truck-loading text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý nhập hàng</span>
                </div>
            </a>

            <a href="{{ route('courts.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-th-large text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý sân</span>
                </div>
            </a>

            <a href="{{ route('court-rates.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-money-bill-wave text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý giá thuê</span>
                </div>
            </a>

            <a href="{{ route('promotions.index') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-calendar-day text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý khuyễn mãi</span>
                </div>
            </a>

            <a href="{{ route('owner.statistical') }}" class="sidebar-item group">
                <div class="flex items-center p-4 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mx-2 transition-all duration-200">
                    <i class="fas fa-chart-line text-xl min-w-[24px]"></i>
                    <span class="ml-4 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Quản lý thống kê</span>
                </div>
            </a>
        </div>
    </div>
</session>

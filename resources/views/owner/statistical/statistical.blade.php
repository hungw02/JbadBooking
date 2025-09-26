@extends('layout.main-owner')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Quản lý thống kê</h1>

    <!-- Filters -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Thời gian</label>
                <select id="period-filter" class="px-3 py-2 border rounded-md">
                    <option value="day">Theo ngày</option>
                    <option value="week">Theo tuần</option>
                    <option value="month" selected>Theo tháng</option>
                    <option value="year">Theo năm</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Từ ngày</label>
                <input type="date" id="start-date" class="px-3 py-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Đến ngày</label>
                <input type="date" id="end-date" class="px-3 py-2 border rounded-md">
            </div>
            <div class="mt-6">
                <button id="apply-filter" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Áp dụng</button>
            </div>
        </div>
    </div>

    <!-- Revenue Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 col-span-1">
            <h3 class="text-sm font-medium text-gray-500">Tổng doanh thu</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenue['total']) }} ₫</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 col-span-1">
            <h3 class="text-sm font-medium text-gray-500">Đặt sân theo buổi</h3>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($totalRevenue['single_bookings']) }} ₫</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 col-span-1">
            <h3 class="text-sm font-medium text-gray-500">Đặt sân định kỳ</h3>
            <p class="text-2xl font-bold text-pink-600">{{ number_format($totalRevenue['subscription_bookings']) }} ₫</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 col-span-1">
            <h3 class="text-sm font-medium text-gray-500">Doanh thu bán hàng</h3>
            <p class="text-2xl font-bold text-green-600">{{ number_format($totalRevenue['sales']) }} ₫</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 col-span-1">
            <h3 class="text-sm font-medium text-gray-500">Doanh thu cho thuê</h3>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalRevenue['rentals']) }} ₫</p>
        </div>
    </div>

    <!-- Revenue Chart & Revenue by Source -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200 lg:col-span-2">
            <h2 class="text-lg font-semibold mb-4">Biểu đồ doanh thu</h2>
            <div style="height: 400px; max-height: 500px;">
                <canvas id="revenue-chart"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">Tỷ lệ doanh thu theo nguồn</h2>
            <div style="height: 400px; max-height: 500px;">
                <canvas id="revenue-source-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Courts & Products Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Courts -->
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">Doanh thu từ các sân</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên sân</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Doanh thu</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượt đặt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topCourts as $court)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $court['name'] }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($court['total_revenue']) }} ₫</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">{{ $court['booking_count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">Doanh thu từ các sản phẩm</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topProducts as $product)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $product['name'] }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">{{ number_format($product['revenue']) }} ₫</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Booking Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Bookings by Day of Week -->
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">Lượng đặt sân theo ngày trong tuần</h2>
            <div style="height: 400px; max-height: 500px;">
                <canvas id="bookings-by-day-chart"></canvas>
            </div>
        </div>

        <!-- Peak Hours -->
        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">Giờ cao điểm</h2>
            <div style="height: 400px; max-height: 500px;">
                <canvas id="peak-hours-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Court Usage -->
    <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
        <h2 class="text-lg font-semibold mb-4">Lượng đơn đặt theo sân</h2>
        <div style="height: 400px; max-height: 500px;">
            <canvas id="court-usage-chart"></canvas>
        </div>
    </div>
</div>

<div id="revenue-data" data-revenue="{{ json_encode($revenueBySource) }}" style="display: none;"></div>
@endsection

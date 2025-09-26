@extends('layout.main-owner')

@section('title', 'Quản lý đơn đặt')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý đơn đặt</h1>
    </div>

    <!-- Search and Filter -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl shadow-lg p-8 mb-8">
        <form action="{{ route('owner.bookings.index') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                <!-- Tìm kiếm -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-800 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Tên khách hàng hoặc mã đơn"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                </div>

                <!-- Loại đơn đặt -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-800 mb-2">Loại đơn đặt</label>
                    <select name="type" id="type"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm">
                        <option value="all" {{ request('type') == 'all' || !request('type') ? 'selected' : '' }}>Tất cả</option>
                        <option value="single" {{ request('type') == 'single' ? 'selected' : '' }}>Đơn đặt theo buổi</option>
                        <option value="subscription" {{ request('type') == 'subscription' ? 'selected' : '' }}>Đơn đặt định kỳ</option>
                    </select>
                </div>

                <!-- Sân -->
                <div>
                    <label for="court_id" class="block text-sm font-medium text-gray-800 mb-2">Sân</label>
                    <select name="court_id" id="court_id"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                        <option value="">Tất cả sân</option>
                        @foreach(\App\Models\Court::orderBy('name')->get() as $court)
                        <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>{{ $court->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Trạng thái -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-800 mb-2">Trạng thái</label>
                    <select name="status" id="status"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <!-- Từ ngày -->
                <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-800 mb-2">Từ ngày</label>
                    <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                </div>

                <!-- Đến ngày -->
                <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-800 mb-2">Đến ngày</label>
                    <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                </div>

                <!-- Loại thanh toán -->
                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-800 mb-2">Loại thanh toán</label>
                    <select name="payment_type" id="payment_type"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                        <option value="">Tất cả</option>
                        <option value="deposit" {{ request('payment_type') == 'deposit' ? 'selected' : '' }}>Đặt cọc</option>
                        <option value="full" {{ request('payment_type') == 'full' ? 'selected' : '' }}>Thanh toán toàn bộ</option>
                    </select>
                </div>

                <!-- Phương thức thanh toán -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-800 mb-2">Phương thức thanh toán</label>
                    <select name="payment_method" id="payment_method"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-100 px-4 py-2 text-sm shadow-sm focus:outline-none">
                        <option value="">Tất cả</option>
                        <option value="vnpay" {{ request('payment_method') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                        <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>Ví cá nhân</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-300 mt-6">
                <a href="{{ route('owner.bookings.index') }}"
                    class="flex items-center bg-gray-500 hover:bg-gray-600 text-white font-medium px-5 py-2 rounded-lg shadow transition">
                    <i class="fas fa-redo-alt mr-2"></i>Đặt lại
                </a>
                <button type="submit"
                    class="flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow transition">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Single Bookings Table -->
    @if($type != 'subscription')
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Đơn Đặt Sân Theo Buổi</h2>
        </div>

        @if($singleBookings->isEmpty())
        <div class="px-6 py-4 text-gray-500 text-center">
            Không tìm thấy đơn đặt sân nào.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sân</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại TT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức TT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($singleBookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->customer->fullname }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->customer->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->court->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($booking->total_price) }} đ</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->payment_type == 'deposit' ? 'Đặt cọc' : 'Thanh toán toàn bộ' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->payment_method == 'vnpay' ? 'VNPay' : 'Ví cá nhân' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_time)->isFuture()) bg-green-100 text-green-800
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_time)->isPast()) bg-blue-100 text-blue-800
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                                @endif">
                                @if($booking->status == 'pending') Chờ xác nhận
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_time)->isFuture()) Đã xác nhận
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_time)->isPast()) Hoàn thành
                                @elseif($booking->status == 'cancelled') Đã hủy
                                @elseif($booking->status == 'completed') Hoàn thành
                                @endif
                            </span>
                            @if($booking->status == 'cancelled' && $booking->refunds->count() > 0)
                            <div class="text-xs text-gray-500 mt-1">
                                Hoàn tiền: {{ number_format($booking->refunds->first()->refund_amount) }} Xu
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('owner.bookings.single', $booking->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            @if($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_time)->isPast())
                            <form action="{{ route('owner.bookings.complete', $booking->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-check"></i> Hoàn thành
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <x-pagination :paginator="$singleBookings" />
        </div>
        @endif
    </div>
    @endif

    <!-- Subscription Bookings Table -->
    @if($type != 'single')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Đơn Đặt Sân Định Kỳ</h2>
        </div>

        @if($subscriptionBookings->isEmpty())
        <div class="px-6 py-4 text-gray-500 text-center">
            Không tìm thấy đơn đặt sân định kỳ nào.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sân</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thứ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Từ - Đến</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($subscriptionBookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->customer->username }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->customer->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->court->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @php
                                $days = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                                echo $days[$booking->day_of_week];
                                @endphp
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($booking->total_price) }} đ</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_date)->isFuture()) bg-green-100 text-green-800
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_date)->isPast()) bg-blue-100 text-blue-800
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                                @endif">
                                @if($booking->status == 'pending') Chờ xác nhận
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_date)->isFuture()) Đã xác nhận
                                @elseif($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_date)->isPast()) Hoàn thành
                                @elseif($booking->status == 'cancelled') Đã hủy
                                @elseif($booking->status == 'completed') Hoàn thành
                                @endif
                            </span>
                            @if($booking->status == 'cancelled' && $booking->refunds->count() > 0)
                            <div class="text-xs text-gray-500 mt-1">
                                Hoàn tiền: {{ number_format($booking->refunds->first()->refund_amount) }} Xu
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('owner.bookings.subscription', $booking->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                            @if($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_date)->isPast())
                            <form action="{{ route('owner.bookings.complete', $booking->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-check"></i> Hoàn thành
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <x-pagination :paginator="$subscriptionBookings" />
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
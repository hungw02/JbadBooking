@extends('layout.main-owner')

@section('title', 'Chi tiết đơn đặt theo buổi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('owner.bookings.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Chi Tiết Đơn Đặt Sân Định Kỳ #{{ $booking->id }}</h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('owner.bookings.subscription.print', $booking->id) }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition" target="_blank">
                <i class="fas fa-print mr-2"></i>In hóa đơn
            </a>
        </div>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Booking Info -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-800">Thông Tin Đơn Đặt Định Kỳ</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Mã đơn đặt</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $booking->id }}</p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Trạng thái</h3>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                        @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                                        @endif">
                                        @if($booking->status == 'pending') Chờ xác nhận
                                        @elseif($booking->status == 'confirmed') Đã xác nhận
                                        @elseif($booking->status == 'cancelled') Đã hủy
                                        @elseif($booking->status == 'completed') Hoàn thành
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Thời gian đặt</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Thứ</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    @php
                                        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                                        echo $days[$booking->day_of_week];
                                    @endphp
                                </p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Thời gian sử dụng</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Thời gian áp dụng</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Sân</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $booking->court->name }}</p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Phương thức thanh toán</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($booking->payment_method == 'vnpay') VNPay
                                    @elseif($booking->payment_method == 'wallet') Ví cá nhân
                                    @endif
                                </p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Loại thanh toán</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($booking->payment_type == 'full') Toàn bộ
                                    @elseif($booking->payment_type == 'deposit') Đặt cọc
                                    @else {{ $booking->payment_type }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-4">Thông tin giá</h3>
                            
                            @php
                            // Tính số buổi cho hiển thị
                            $startDate = \Carbon\Carbon::parse($booking->start_date);
                            $endDate = \Carbon\Carbon::parse($booking->end_date);
                            $dayOfWeek = $booking->day_of_week;
                            
                            // Đếm số buổi chơi trong kỳ hạn
                            $sessionCount = 0;
                            $date = clone $startDate;
                            while ($date <= $endDate) {
                                if ((int)$date->format('N') + 1 == $dayOfWeek) {
                                    $sessionCount++;
                                }
                                $date->addDay();
                            }
                            
                            // Tính đơn giá mỗi buổi
                            $pricePerSession = $sessionCount > 0 ? round($booking->getOriginalPrice() / $sessionCount) : 0;
                            @endphp
                            
                            <div class="mb-2 flex justify-between">
                                <span class="text-sm text-gray-600">Số buổi:</span>
                                <span class="text-sm text-gray-900">{{ $sessionCount }} buổi</span>
                            </div>
                            
                            <div class="mb-2 flex justify-between">
                                <span class="text-sm text-gray-600">Đơn giá / buổi:</span>
                                <span class="text-sm text-gray-900">{{ number_format($booking->getOriginalPricePerSession()) }} đ</span>
                            </div>
                            
                            <div class="mb-2 flex justify-between">
                                <span class="text-sm text-gray-600">Tổng giá gốc:</span>
                                <span class="text-sm text-gray-900">{{ number_format($booking->getOriginalPrice()) }} đ</span>
                            </div>
                            
                            @if($booking->discount_percent > 0)
                            <div class="mb-2 flex justify-between">
                                <span class="text-sm text-gray-600">Giảm giá ({{ $booking->discount_percent }}%):</span>
                                <span class="text-sm text-red-600">-{{ number_format($booking->getDiscountAmount()) }} đ</span>
                            </div>
                            @endif
                            
                            <div class="mb-2 flex justify-between font-medium bg-green-50 p-2 rounded">
                                <span class="text-sm text-gray-700">Số tiền thanh toán:</span>
                                <span class="text-sm text-green-700 font-bold">{{ number_format($booking->total_price) }} đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-800">Thông Tin Khách Hàng</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Tên khách hàng</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $booking->user->fullname }}</p>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $booking->user->email }}</p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Số điện thoại</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $booking->user->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($booking->status == 'cancelled' && $booking->refunds->count() > 0)
            <!-- Refund Info -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-800">Thông Tin Hoàn Tiền</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Số tiền hoàn trả</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ number_format($booking->refunds->first()->refund_amount) }} Xu</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Lý do hoàn tiền</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $booking->refunds->first()->refund_reason }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Thời gian hoàn tiền</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->refunds->first()->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="md:col-span-1">
            <!-- Cancel Booking Form -->
            @if($booking->status == 'pending' || $booking->status == 'confirmed')
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 bg-red-50 border-b border-red-100">
                    <h2 class="text-lg font-medium text-red-800">Hủy Đơn Đặt Sân</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('owner.bookings.subscription.cancel', $booking->id) }}" method="POST" data-confirm="Bạn có chắc chắn muốn hủy đơn đặt sân định kỳ này không?">
                        @csrf
                        <div class="mb-4">
                            <label for="refund_amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền hoàn trả</label>
                            @php
                                $maxRefund = ($booking->payment_type == 'deposit') ? ($booking->total_price * 0.5) : $booking->total_price;
                            @endphp
                            <input type="number" max="{{ $maxRefund }}" min="0" name="refund_amount" id="refund_amount" value="{{ $maxRefund }}" class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-400 focus:border-red-400 shadow-sm focus:outline-none">
                            <p class="mt-1 text-xs text-gray-500">Số tiền tối đa: {{ number_format($maxRefund) }} Xu</p>
                        </div>
                        <div class="mb-4">
                            <label for="refund_reason" class="block text-sm font-medium text-gray-700 mb-1">Lý do hủy</label>
                            <textarea name="refund_reason" placeholder="Nhập lý do hủy đơn đặt định kỳ" id="refund_reason" rows="3" class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-400 focus:border-red-400 shadow-sm focus:outline-none" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition">
                            Hủy đơn đặt sân
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-800">Trạng thái đơn hàng</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 text-center">
                            @if($booking->status == 'cancelled')
                            Đơn đặt sân định kỳ đã bị hủy, không thể chỉnh sửa hoặc hủy lại.
                            @elseif($booking->status == 'completed')
                            Đơn đặt sân định kỳ đã hoàn thành, không thể chỉnh sửa hoặc hủy.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Edit Booking Form -->
            @if($booking->status == 'pending' || $booking->status == 'confirmed')
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                    <h2 class="text-lg font-medium text-blue-800">Sửa Thông Tin Đặt Sân</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('owner.bookings.subscription.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="court_id" class="block text-sm font-medium text-gray-700 mb-1">Sân</label>
                            <select name="court_id" id="court_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none">
                                @foreach($courts as $court)
                                    @if(in_array($court->id, $availableCourtIds))
                                    <option value="{{ $court->id }}" {{ $booking->court_id == $court->id ? 'selected' : '' }}>
                                        {{ $court->name }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Chỉ hiển thị những sân còn trống trong khung giờ hàng tuần này</p>
                        </div>
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu</label>
                            <input type="time" name="start_time" id="start_time" value="{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none @error('start_time') border-red-500 @enderror">
                            @error('start_time')
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('start_time') }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Giờ kết thúc</label>
                            <input type="time" name="end_time" id="end_time" value="{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none @error('end_time') border-red-500 @enderror">
                            @error('end_time')
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('end_time') }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                            <input type="date" name="start_date" id="start_date" value="{{ \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('start_date') }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                            <input type="date" name="end_date" id="end_date" value="{{ \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 focus:outline-none @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('end_date') }}</p>
                            @enderror
                        </div>
                        <div class="bg-blue-50 p-3 rounded-md mb-4">
                            <ul class="text-xs text-blue-700 list-disc list-inside">
                                <li>Thời gian chơi hàng tuần phải có sẵn trong cùng khung giờ</li>
                                <li>Chỉ có thể đặt sân trong khung giờ chưa có booking khác</li>
                                <li>Đảm bảo thời gian bắt đầu và kết thúc hợp lý</li>
                            </ul>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition">
                            Cập nhật thông tin
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 
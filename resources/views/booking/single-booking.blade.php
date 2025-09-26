@extends('layout.main-customer')

@section('title', 'Đặt sân theo buổi')

@section('content')
<div class="max-w-7xl mx-auto p-5">
    <h1 class="text-center text-2xl font-bold text-white mb-8 bg-blue-600 p-3 rounded-lg">Đặt sân theo buổi</h1>

    @if(session('error'))
    <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded mb-5">
        {{ session('error') }}
    </div>
    @endif

    <!-- Thông báo khi bị trùng -->
    @if(session('conflicts'))
    <div class="bg-yellow-100 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-5">
        <h4 class="font-bold">Đã có lịch đặt sân trong khung giờ bạn chọn</h4>
        <div>{!! session('conflicts') !!}</div>
        <p>Vui lòng chọn giờ hoặc sân khác.</p>
    </div>
    @endif

    <form id="bookingForm" action="{{ route('booking.single.store') }}" method="POST">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Chọn thời gian bạn muốn chơi -->
        <div class="bg-gray-50 p-5 rounded-lg shadow-sm mb-8">
            <h2 class="text-xl text-blue-600 pb-3 border-b border-gray-200">1. Chọn thời gian bạn muốn chơi</h2>
            <!-- Ngày -->
            <div class="mt-6 flex gap-5 items-center">
                <label for="booking-date" class="block font-semibold mb-1">Ngày:</label>
                <input type="date" id="booking-date" name="date" min="{{ date('Y-m-d') }}" value="{{ old('date', date('Y-m-d')) }}" required
                    class="w-1/4 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none">
                <div id="day-of-week-display" class="text-gray-600 text-sm mt-1"></div>
            </div>
            <!-- Giờ chơi -->
            <div class="mt-6 flex gap-5 items-center">
                <div class="w-1/2 flex items-center">
                    <label for="start-time" class="w-1/4 block font-semibold">Giờ bắt đầu:</label>
                    <select id="start-time" name="start_time" required
                        class="w-1/2 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none time-select">
                        @foreach(range(5, 23) as $hour)
                        @foreach([0, 30] as $minute)
                        <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}"
                            {{ old('start_time') == sprintf('%02d:%02d', $hour, $minute) ? 'selected' : '' }}>
                            {{ sprintf('%02d:%02d', $hour, $minute) }}
                        </option>
                        @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="w-1/2 flex items-center">
                    <label for="end-time" class="w-1/4 block font-semibold">Giờ kết thúc:</label>
                    <select id="end-time" name="end_time" required
                        class="w-1/2 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none time-select">
                        @foreach(range(6, 23) as $hour)
                        @foreach([0, 30] as $minute)
                        <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}"
                            {{ old('end_time') == sprintf('%02d:%02d', $hour, $minute) ? 'selected' : '' }}>
                            {{ sprintf('%02d:%02d', $hour, $minute) }}
                        </option>
                        @endforeach
                        @endforeach
                        <option value="00:00" {{ old('end_time') == '00:00' ? 'selected' : '' }}>00:00</option>
                    </select>
                </div>
            </div>
            <div id="time-error" class="pb-5 text-red-500 text-sm mt-2"></div>
            <!-- Thông báo giá dự kiến -->
            <div id="price-preview" class="hidden mb-5 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <i class="fa-solid fa-clock text-blue-500 mr-2"></i>
                    <span class="font-semibold">Thời gian: <span id="preview-hours" class="text-blue-700">0 giờ</span></span>
                </div>
                <div class="flex items-center mt-2">
                    <i class="fa-solid fa-money-bill-wave text-blue-500 mr-2"></i>
                    <span class="font-semibold">Giá: <span id="preview-price" class="text-blue-700">0 đ</span>/sân</span>
                </div>
            </div>
            <!-- Chọn sân -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-3">Tình trạng sân</h3>
                <!-- Các trạng thái sân -->
                <div class="flex flex-wrap items-center space-x-6 mb-5">
                    <div class="flex items-center">
                        <div class="inline-block w-4 h-4 rounded-md mr-1 available"></div>
                        <span class="text-sm">Trống</span>
                    </div>
                    <div class="flex items-center">
                        <div class="inline-block w-4 h-4 rounded-md mr-1 single-booking"></div>
                        <span class="text-sm">Đã đặt theo buổi</span>
                    </div>
                    <div class="flex items-center">
                        <div class="inline-block w-4 h-4 rounded-md mr-1 subscription-booking"></div>
                        <span class="text-sm">Đã đặt đinh kỳ</span>
                    </div>
                    <div class="flex items-center ml-6">
                        <div class="inline-block w-4 h-4 rounded-md mr-1 selected"></div>
                        <span class="text-sm">Đang chọn</span>
                    </div>
                </div>
                <!-- Lịch trống -->
                <div class="overflow-x-auto pb-3">
                    <div class="court-timeline">
                        <!-- Các giờ cho thuê trong ngày -->
                        <div class="time-header ml-1">
                            <div class="time-header-item text-right">Giờ / Sân</div>
                            @for($i = 5; $i <= 23; $i++)
                                <div class="time-header-item text-right">{{ $i }}
                        </div>
                        @endfor
                        <div class="time-header-item text-right">0</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chọn sân -->
        <div class="space-y-4">
            @foreach($courts as $court)
            <div class="court-card flex items-center" data-court-id="{{ $court->id }}">
                <div class="w-28 flex-shrink-0">
                    <div class="h-full flex flex-col justify-between py-1">
                        <label class="court-select-container">
                            <img src="{{ asset($court->image) }}" alt="Sân {{ $court->name }}" class="h-16 object-cover rounded pr-2">
                            <input type="checkbox" name="court_ids[]" value="{{ $court->id }}" class="court-select"
                                {{ in_array($court->id, old('court_ids', [])) ? 'checked' : '' }}>
                            <span class="checkbox-custom"></span>
                        </label>
                    </div>
                </div>

                <div class="flex-1 rounded relative court-timeline" data-court-id="{{ $court->id }}">
                    <div class="time-slots">
                        <!-- Các giờ trống sẽ được tải vào đây -->
                        <div class="col-span-19 h-full flex justify-center items-center text-sm text-gray-500">
                            <span class="animate-pulse">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
</div>

<!-- Kiểm tra thông tin đặt sân -->
<div class="bg-blue-50 p-5 rounded-lg shadow-sm mb-8">
    <h2 class="text-xl text-blue-600 mb-4">2. Kiểm tra thông tin đặt sân</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex justify-between p-3 bg-white rounded">
            <span>Tổng thời gian:</span>
            <span id="total-hours" class="font-semibold">0 giờ</span>
        </div>
        <div class="flex justify-between p-3 bg-white rounded">
            <span>Sân đánh:</span>
            <span id="total-courts" class="font-semibold">Chưa chọn</span>
        </div>
        <div class="flex justify-between p-3 bg-white rounded">
            <span>Tổng giá:</span>
            <span id="total-price" class="font-semibold">0</span>
        </div>
        <div class="flex justify-between p-3 bg-white rounded">
            <span>Khuyến mãi:</span>
            <span id="promotion-name" class="font-semibold text-green-600">Không có</span>
        </div>
        <div class="flex justify-between p-3 bg-white rounded col-span-full">
            <div class="flex justify-between items-center">
                <div id="original-price-display" class="text-sm text-gray-600 hidden"></div>
            </div>
            <div class="flex justify-between items-center">
                <div id="discount-display" class="text-sm text-green-600 font-medium hidden"></div>
            </div>
        </div>
        <div class="flex justify-between p-3 bg-green-100 rounded font-semibold col-span-full">
            <span>Số tiền thanh toán:</span>
            <span id="payment-amount">0 VNĐ</span>
        </div>
        <div id="court-error" class="text-red-500 text-sm text-center col-span-full"></div>
    </div>
</div>

<input type="hidden" id="promotion-select" name="promotion_id" value="">

<!-- Phương thức thanh toán -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg shadow-md mb-8 border border-blue-100">
    <h2 class="text-xl text-blue-600 mb-4">3. Chọn phương thức thanh toán</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Loại thanh toán -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <label class="block font-semibold mb-4 text-gray-700 flex items-center">
                <i class="fa-solid fa-wallet text-blue-500 mr-2"></i>
                Loại thanh toán
            </label>
            <div class="space-y-3 pl-2">
                <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                    <input type="radio" name="payment_type" value="deposit" required
                        {{ old('payment_type', '') == 'deposit' ? 'checked' : '' }}
                        class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="font-medium">Đặt cọc</span>
                        <p class="text-sm text-gray-500">Thanh toán 50% trước, phần còn lại khi đến sân</p>
                    </div>
                </label>
                <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                    <input type="radio" name="payment_type" value="full" required
                        {{ old('payment_type', 'full') == 'full' ? 'checked' : '' }}
                        class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500" checked>
                    <div>
                        <span class="font-medium">Thanh toán toàn bộ</span>
                        <p class="text-sm text-gray-500">Thanh toán 100% số tiền đặt sân</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <label class="block font-semibold mb-4 text-gray-700 flex items-center">
                <i class="fa-solid fa-credit-card text-blue-500 mr-2"></i>
                Phương thức thanh toán
            </label>
            <div class="space-y-3 pl-2">
                <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                    <input type="radio" name="payment_method" value="vnpay" required
                        {{ old('payment_method', 'vnpay') == 'vnpay' ? 'checked' : '' }}
                        class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500" id="payment_method_vnpay" checked>
                    <div>
                        <div class="flex items-center">
                            <span class="font-medium">VNPay</span>
                            <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR-1024x800.png" alt="VNPay" class="h-6 ml-2">
                        </div>
                        <p class="text-sm text-gray-500">Thanh toán tiện lợi qua VNPay</p>
                    </div>
                </label>
                <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                    <input type="radio" name="payment_method" value="wallet" required
                        {{ old('payment_method', '') == 'wallet' ? 'checked' : '' }}
                        class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500" id="payment_method_wallet">
                    <div>
                        <div class="flex items-center">
                            <span class="font-medium">Ví cá nhân</span>
                            <span class="text-sm bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full ml-2">
                                Số dư: <span id="wallet-balance" class="font-semibold">{{ number_format(auth()->user()->wallets ?? 0) }} Xu</span>
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">Thanh toán nhanh chóng từ số dư ví</p>
                    </div>
                </label>
                <div id="wallet-warning" class="text-red-600 text-sm hidden mt-2 p-3 bg-red-50 rounded-md border-l-4 border-red-500">
                    <div class="flex">
                        <i class="fa-solid fa-circle-exclamation mr-2 mt-0.5"></i>
                        <span>Số dư ví không đủ để thực hiện giao dịch này</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="user-wallet-balance" value="{{ auth()->user()->wallets ?? 0 }}">
</div>

<!-- Xác nhận -->
<div class="flex justify-center gap-4 mt-8">
    <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-colors shadow-md flex items-center" id="submit-booking">
        <i class="fa-solid fa-check-circle mr-2"></i> Xác nhận đặt sân
    </button>
    <a href="{{ route('booking.index') }}" class="bg-gray-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors shadow-md flex items-center">
        <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
    </a>
</div>
</form>
</div>
@endsection
@extends('layout.main-customer')

@section('title', 'Chi tiết lịch đặt theo buổi')

@section('content')
<div class="max-w-7xl mx-auto p-5">
    <div class="mb-4 flex items-center">
        <a href="{{ route('booking.list') }}" class="text-blue-600 hover:underline flex items-center">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded mb-5">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded mb-5">
        {{ session('error') }}
    </div>
    @endif

    @if(isset($booking))
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 p-4">
            <h1 class="text-white text-xl font-bold">Chi tiết lịch đặt #{{ $booking->id }}</h1>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Thông tin cơ bản -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <h2 class="text-lg font-semibold text-blue-800 mb-4 pb-2 border-b border-blue-200">
                        <i class="fa-solid fa-info-circle mr-2"></i>Thông tin lịch đặt
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sân:</span>
                            <span class="font-medium">{{ $booking->court->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ngày chơi:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian đặt lịch:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trạng thái:</span>
                            <span class="">
                                @if($booking->status == 'confirmed')
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Đã xác nhận</span>
                                @elseif($booking->status == 'completed')
                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">Đã hoàn thành</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Đã hủy</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Thông tin thanh toán -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <h2 class="text-lg font-semibold text-green-800 mb-4 pb-2 border-b border-green-200">
                        <i class="fa-solid fa-money-bill-wave mr-2"></i>Thông tin thanh toán
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tổng tiền thuê sân:</span>
                            <span class="font-medium">{{ number_format($booking->total_price) }} đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Đã thanh toán:</span>
                            <span class="font-medium text-green-600">{{ number_format($booking->payment_type === 'deposit' ? $booking->total_price * 0.5 : $booking->total_price) }} đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Còn lại:</span>
                            <span class="font-medium {{ $booking->payment_type === 'deposit' ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($booking->payment_type === 'deposit' ? $booking->total_price * 0.5 : 0) }} đ
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hình thức thanh toán:</span>
                            <span class="font-medium">{{ $booking->payment_type === 'deposit' ? 'Đặt cọc (50%)' : 'Thanh toán đầy đủ' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phương thức thanh toán:</span>
                            <span class="font-medium">
                                @if($booking->payment_method == 'vnpay')
                                    <span class="flex items-center">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR-1024x800.png" alt="VNPay" class="h-5 mr-1">
                                        VNPay
                                    </span>
                                @else
                                    <span class="flex items-center">
                                        <i class="fa-solid fa-wallet mr-1 text-blue-500"></i>
                                        Ví cá nhân
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin hoàn tiền (nếu đã hủy) -->
            @if($booking->status == 'cancelled')
                @if($booking->refunds && $booking->refunds->count() > 0)
                <div class="mt-6 bg-orange-50 rounded-lg p-4 border border-orange-100">
                    <h2 class="text-lg font-semibold text-orange-800 mb-4 pb-2 border-b border-orange-200">
                        <i class="fa-solid fa-money-bill-transfer mr-2"></i>Thông tin hoàn tiền
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Số tiền hoàn trả:</span>
                            <span class="font-medium text-orange-600">{{ number_format($booking->refund->refund_amount) }} Xu</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lý do hoàn tiền:</span>
                            <span class="font-medium">{{ $booking->refund->refund_reason }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Thời gian hoàn tiền:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->refund->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phương thức hoàn tiền:</span>
                            <span class="font-medium flex items-center">
                                <i class="fa-solid fa-wallet mr-1 text-blue-500"></i>
                                Ví cá nhân
                            </span>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-6 bg-gray-100 rounded-lg p-4 border border-gray-200">
                    <p>Đặt sân đã bị hủy nhưng không có thông tin hoàn tiền.</p>
                </div>
                @endif
            @endif

            <!-- Tùy chọn cho lịch đặt -->
            @if($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->start_time) > now())
            <div class="mt-6 flex flex-wrap gap-3 justify-center">
                <a href="{{ route('booking.change', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition">
                    <i class="fa-solid fa-exchange-alt mr-2"></i> Đổi sân
                </a>
                <form action="{{ route('booking.single.cancel', $booking->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch đặt này?')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition">
                        <i class="fa-solid fa-times-circle mr-2"></i> Hủy lịch đặt
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="bg-yellow-100 border border-yellow-200 text-yellow-800 p-4 rounded">
        <p>Không tìm thấy thông tin lịch đặt hoặc bạn không có quyền xem thông tin này.</p>
    </div>
    @endif

    <div class="mt-8">
        <h2 class="text-white text-lg font-semibold mb-4">
            <i class="fa-solid fa-circle-info mr-2"></i>Quy định về việc hủy sân
        </h2>
        <div class="bg-white p-4 rounded-lg shadow">
            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                <li>Hủy trong vòng 5 phút sau khi đặt: hoàn 100% số tiền đã thanh toán.</li>
                <li>Hủy trước giờ chơi trên 24 tiếng: hoàn 50% số tiền đã thanh toán.</li>
                <li>Hủy trước giờ chơi từ 12-24 tiếng: hoàn 25% số tiền đã thanh toán.</li>
                <li>Hủy trong vòng 12 tiếng trước giờ chơi: không hoàn phí.</li>
                <li>Lưu ý: Số tiền hoàn trả được tính dựa trên số tiền bạn đã thực tế thanh toán.</li>
                <li>Số tiền hoàn sẽ được chuyển vào ví cá nhân, sử dụng cho lần tới đặt sân.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

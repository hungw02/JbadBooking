@extends('layout.main-customer')

@section('title', 'Đặt lịch thành công')

@section('content')
<div class="max-w-4xl mx-auto p-5">
    <div class="text-center mb-10">
        <i class="fa-solid fa-circle-check text-green-600 text-7xl mb-5"></i>
        <h1 class="text-2xl md:text-3xl font-bold text-green-600 mb-2">Đặt sân thành công!</h1>
        <p class="text-white">Cảm ơn bạn đã đặt sân tại JBADMINTON</p>
    </div>

    @if($bookings->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-xl font-bold text-blue-600 mb-5 pb-2 border-b border-gray-200">Chi tiết lịch đặt theo buổi</h2>

        @php
        // Lấy đơn đặt sân mới nhất
        $booking = $bookings->first();
        @endphp

        <div class="bg-gray-50 rounded-lg shadow-sm mb-5 overflow-hidden">
            <div class="bg-blue-50 p-4 flex flex-col md:flex-row justify-between items-start md:items-center">
                <h3 class="font-bold text-blue-600">Sân {{ $booking->court->name }}</h3>
                <span class="text-sm text-gray-600 mt-1 md:mt-0">Mã đơn đặt: #{{ $booking->id }}</span>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Thời gian:</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Ngày:</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Loại thanh toán:</p>
                        <p class="font-semibold">{{ $booking->payment_type == 'deposit' ? 'Đặt cọc' : 'Thanh toán toàn bộ' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Phương thức thanh toán:</p>
                        <p class="font-semibold">{{ $booking->payment_method == 'vnpay' ? 'VNPay' : 'Ví cá nhân' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tổng tiền:</p>
                        <p class="font-semibold">{{ number_format($booking->total_price) }} {{ $booking->payment_method == 'vnpay' ? 'VNĐ' : 'Xu' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Trạng thái:</p>
                        <p class="font-semibold">{{ $booking->status == 'confirmed' ? 'Đã xác nhận' : 'Đã hủy' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-100 p-4 border-t border-gray-200">
                @if($booking->status == 'confirmed')
                <form action="{{ route('booking.single.cancel', $booking) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded transition" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt sân này?')">
                        <i class="fa-solid fa-times mr-2"></i> Hủy đặt sân
                    </button>
                </form>
                @else
                <span class="inline-flex items-center bg-gray-500 text-white font-semibold px-4 py-2 rounded">
                    <i class="fa-solid fa-ban mr-2"></i> Đã hủy đặt sân
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 p-5 rounded-lg mb-10">
        <h2 class="text-xl font-bold text-yellow-700 mb-4">Một số điều bạn cần lưu ý</h2>
        <ul class="list-disc pl-5 space-y-2">
            <li>Vui lòng đến sân đúng giờ</li>
            <li>Quý khách có thể mang đồ ăn vào sân nhưng vui lòng giữ gìn sân vệ sinh chung</li>
            <li>Nhận sân và trả sân đúng giờ đã đặt</li>
            <li>Tôn trọng, nâng cao tinh thần thể thao, giao lưu, học hỏi</li>
            <li>Thanh toán số tiền còn lại (nếu chỉ đặt cọc) khi đến sân</li>
            <li>Trường hợp huỷ sân:
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Hủy trong vòng 5 phút sau khi đặt: hoàn 100% phí.</li>
                    <li>Hủy trước giờ chơi trên 24 tiếng: hoàn 50% phí.</li>
                    <li>Hủy trước giờ chơi từ 12-24 tiếng: hoàn 25% phí.</li>
                    <li>Hủy trong vòng 12 tiếng trước giờ chơi: không hoàn phí</li>
                    <li>Số tiền hoàn sẽ được chuyển vào ví cá nhân, sử dụng cho lần tới đặt sân.</li>
                </ul>
            </li>
        </ul>
    </div>
    @else
    <div class="bg-red-100 text-red-800 p-6 rounded-lg text-center mb-8">
        <p>Không tìm thấy thông tin đặt sân. Vui lòng kiểm tra lại.</p>
    </div>
    @endif

    <div class="flex justify-center space-x-4">
        <a href="{{ route('home') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded transition">Về trang chủ</a>
        <a href="{{ route('booking.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold px-5 py-3 rounded transition">Đặt sân khác</a>
    </div>
</div>
@endsection
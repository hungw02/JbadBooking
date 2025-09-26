@extends('layout.main-customer')

@section('title', 'Đặt lịch thành công')

@section('content')
<div class="max-w-4xl mx-auto p-5">
    <div class="text-center mb-10">
        <i class="fa-solid fa-circle-check text-green-600 text-7xl mb-5"></i>
        <h1 class="text-2xl md:text-3xl font-bold text-green-600 mb-2">Đặt sân định kỳ thành công!</h1>
        <p class="text-white">Cảm ơn bạn đã đăng ký sân định kỳ tại JBADMINTON</p>
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

    @if($subscription)
    <div class="mb-10">
        <h2 class="text-xl font-bold text-blue-600 mb-5 pb-2 border-b border-gray-200">Chi tiết lịch đặt định kỳ</h2>

        <div class="bg-gray-50 rounded-lg shadow-sm mb-5 overflow-hidden">
            <div class="bg-blue-50 p-4 flex flex-col md:flex-row justify-between items-start md:items-center">
                <h3 class="font-bold text-blue-600">Sân {{ $subscription->court->name }}</h3>
                <span class="text-sm text-gray-600 mt-1 md:mt-0">Mã đơn đặt: #{{ $subscription->id }}</span>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Ngày đặt hàng tuần:</p>
                        <p class="font-semibold">
                            {{ App\Models\CourtRate::getDayNameStatic($subscription->day_of_week) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Thời gian:</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($subscription->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($subscription->end_time)->format('H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Thời hạn:</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($subscription->start_date)->format('d/m/Y') }} -
                            {{ \Carbon\Carbon::parse($subscription->end_date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tổng số buổi:</p>
                        <p class="font-semibold">
                            @php
                            $startDate = \Carbon\Carbon::parse($subscription->start_date);
                            $endDate = \Carbon\Carbon::parse($subscription->end_date);
                            $dayOfWeek = $subscription->day_of_week;

                            $sessionCount = 0;
                            $currentDate = clone $startDate;

                            while ($currentDate <= $endDate) {
                                $carbonDayOfWeek=$currentDate->dayOfWeek;
                                $ourDayOfWeek = $carbonDayOfWeek + 1;

                                if ($ourDayOfWeek == $dayOfWeek) {
                                $sessionCount++;
                                }

                                $currentDate->addDay();
                                }
                                @endphp
                                {{ $sessionCount }} buổi
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Loại thanh toán:</p>
                        <p class="font-semibold">{{ $subscription->payment_type == 'deposit' ? 'Đặt cọc' : 'Thanh toán toàn bộ' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Phương thức thanh toán:</p>
                        <p class="font-semibold">{{ $subscription->payment_method == 'vnpay' ? 'VNPay' : 'Ví cá nhân' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tổng tiền:</p>
                        <p class="font-semibold">{{ number_format($subscription->total_price) }} Xu</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Trạng thái:</p>
                        <p class="font-semibold">{{ $subscription->status == 'canceled' ? 'Đã hủy' : 'Đã xác nhận' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-100 p-4 border-t border-gray-200">
                @if($subscription->status != 'canceled')
                <form action="{{ route('booking.subscription.cancel', $subscription) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded transition" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt sân định kỳ này?')">
                        <i class="fa-solid fa-times mr-2"></i> Hủy đặt sân định kỳ
                    </button>
                </form>
                @else
                <span class="inline-flex items-center bg-gray-500 text-white font-semibold px-4 py-2 rounded">
                    <i class="fa-solid fa-ban mr-2"></i> Đã hủy đặt sân định kỳ
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
                    <li>Hủy trước ngày chơi đầu tiên trên 24 tiếng: hoàn 50% phí.</li>
                    <li>Hủy trước ngày chơi đầu tiên từ 12-24 tiếng: hoàn 25% phí.</li>
                    <li>Hủy trước ngày chơi đầu tiên dưới 12 tiếng: không hoàn phí</li>
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
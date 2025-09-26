@extends('layout.main-customer')

@section('title', 'Đặt sân cầu lông')

@section('content')
<div class="max-w-7xl mx-auto p-5">
    <div class="flex flex-col md:flex-row justify-between gap-5 mb-10">
        <div class="flex-1 p-6 bg-white rounded-2xl shadow-lg text-center group hover:shadow-2xl transition-shadow duration-500">
            <h2 class="text-2xl text-blue-600 font-semibold mb-3 group-hover:text-blue-700 transition-colors duration-300">
                Đặt sân theo buổi
            </h2>
            <p class="mb-6 min-h-[60px] text-gray-600 text-base group-hover:text-gray-800 transition-colors duration-300">
                Đặt sân linh hoạt theo từng buổi, phù hợp với lịch trình cá nhân
            </p>
            <a href="{{ route('booking.single.create') }}"
                class="inline-block px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-full shadow-md hover:bg-blue-700 hover:scale-105 transition-transform duration-300">
                🚀 Đặt ngay
            </a>
        </div>

        <div class="flex-1 p-6 bg-yellow-100 border-l-4 border-yellow-500 rounded-xl shadow-lg text-center">
            <h2 class="text-2xl font-extrabold text-yellow-700 mb-4 tracking-wide">🔥 Đặt sân định kỳ</h2>
            <p class="mb-5 text-gray-800 min-h-[60px] leading-relaxed">
                Đặt sân theo tuần/tháng, phù hợp cho đội nhóm thường xuyên tập luyện.<br>
                <span class="font-semibold text-green-600">Giảm 10% chi phí thuê sân</span> và <span class="font-semibold text-blue-600">tặng nước uống tại sân!</span>
            </p>
            <a href="{{ route('booking.subscription.create') }}"
                class="inline-block px-6 py-3 bg-yellow-500 text-white font-bold rounded-full shadow-md hover:bg-yellow-600 hover:scale-105 transition-all duration-300">
                🎯 Đặt ngay
            </a>
        </div>
    </div>

    <div class="mt-10 bg-gray-50 p-6 rounded-lg shadow-md">
        <h2 class="font-bold text-xl text-blue-600 mb-5 text-center">Giá thuê sân theo khung giờ</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="p-3 bg-blue-600 text-white border border-gray-300">Khung giờ</th>
                        <th class="p-3 bg-blue-600 text-white border border-gray-300">Thứ 2-6</th>
                        <th class="p-3 bg-blue-600 text-white border border-gray-300">Thứ 7, Chủ Nhật</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courtRates as $rate)
                    <tr class="even:bg-gray-100">
                        <td class="p-3 border border-gray-300 text-center">{{ $rate['time_range']['start'] }} - {{ $rate['time_range']['end'] }}</td>

                        <td class="p-3 border border-gray-300 text-center">
                            @if($rate['weekday_price'])
                            {{ number_format($rate['weekday_price']) }} VNĐ/giờ
                            @else
                            -
                            @endif
                        </td>

                        <td class="p-3 border border-gray-300 text-center">
                            @if($rate['weekend_price'])
                            {{ number_format($rate['weekend_price']) }} VNĐ/giờ
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-10 bg-gray-50 p-6 rounded-lg shadow-md">
        <h2 class="font-bold text-xl text-blue-600 mb-5 text-center">Những điều cần lưu ý trước khi đặt sân</h2>
        <div class="mt-5">
            <h3 class="text-gray-800 my-4 font-medium">Quy định chung</h3>
            <ul class="list-disc pl-5">
                <li class="mb-2">Thời gian thuê sân tối thiểu là 1 giờ</li>
                <li class="mb-2">Khách hàng nên đến sân trước 10 phút để chuẩn bị</li>
                <li class="mb-2">Vui lòng giữ gìn vệ sinh và tôn trọng không gian chung</li>
                <li class="mb-2">Khuyến khích mang giày chuyên dụng để bảo vệ sân</li>
            </ul>

            <h3 class="text-gray-800 my-4 font-medium">Quy định về việc hủy sân</h3>
            <ul class="list-disc pl-5">
                <li class="mb-2"><strong>Hủy trong vòng 5 phút sau khi đặt:</strong> Hoàn 100% phí</li>
                <li class="mb-2"><strong>Hủy trước giờ chơi trên 24 tiếng:</strong> Hoàn 50% phí</li>
                <li class="mb-2"><strong>Hủy trước giờ chơi từ 12 - 24 tiếng:</strong> Hoàn 25% phí</li>
                <li class="mb-2"><strong>Hủy trong vòng 12 tiếng trước giờ chơi:</strong> Không hoàn phí.</li>
            </ul>

            <h3 class="text-gray-800 my-4 font-medium">Phương thức thanh toán</h3>
            <ul class="list-disc pl-5">
                <li class="mb-2"><strong>Đặt cọc:</strong> Thanh toán 50% tổng phí</li>
                <li class="mb-2"><strong>Thanh toán toàn bộ:</strong> Thanh toán 100% tổng phí</li>
                <li class="mb-2">Khách hàng có thể thanh toán qua VNPay hoặc sử dụng số dư trong ví cá nhân</li>
                <li class="mb-2">Sau khi khách hàng đến sân, khách hàng thanh toán phần còn lại</li>
            </ul>
        </div>
    </div>
</div>
@endsection
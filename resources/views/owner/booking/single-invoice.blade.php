<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn đặt sân #{{ $booking->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>

<body class="bg-white p-6">
    <div class="max-w-3xl mx-auto bg-white p-8 border border-gray-200 rounded-lg">
        <!-- Print Button -->
        <div class="flex justify-end mb-6 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition">
                <i class="fas fa-print mr-2"></i>In hóa đơn
            </button>
            <a href="{{ route('owner.bookings.single', $booking->id) }}" style="display: inline-block; margin-left: 10px; padding: 10px 20px; background-color: #f0f0f0; color: #333; text-decoration: none; border-radius: 4px; font-size: 16px;">
                Quay lại
            </a>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">HÓA ĐƠN ĐẶT SÂN</h1>
                <p class="text-gray-600">Mã đơn: #{{ $booking->id }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-semibold text-gray-800">JBadminton</h2>
                <p class="text-gray-600">123 Đường ABC, Quận XYZ</p>
                <p class="text-gray-600">Hà Đông, Hà Nội</p>
                <p class="text-gray-600">0123 456 789</p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Thông tin đơn đặt</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Ngày đặt:</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Trạng thái:</p>
                    <p class="font-medium">
                        @if($booking->status == 'pending') Chờ xác nhận
                        @elseif($booking->status == 'confirmed') Đã xác nhận
                        @elseif($booking->status == 'cancelled') Đã hủy
                        @elseif($booking->status == 'completed') Hoàn thành
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Thời gian sử dụng:</p>
                    <p class="font-medium">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y H:i') }} -
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Sân:</p>
                    <p class="font-medium">{{ $booking->court->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Phương thức thanh toán:</p>
                    <p class="font-medium">
                        @if($booking->payment_method == 'vnpay') VNPay
                        @elseif($booking->payment_method == 'wallet') Ví cá nhân
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Loại thanh toán:</p>
                    <p class="font-medium">
                        @if($booking->payment_type == 'full') Toàn bộ
                        @elseif($booking->payment_type == 'deposit') Đặt cọc
                        @else {{ $booking->payment_type }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Thông tin khách hàng</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Tên khách hàng:</p>
                    <p class="font-medium">{{ $booking->customer->fullname }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Số điện thoại:</p>
                    <p class="font-medium">{{ $booking->customer->phone }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Email:</p>
                    <p class="font-medium">{{ $booking->customer->email }}</p>
                </div>
            </div>
        </div>

        <!-- Price Details -->
        <div class="border-t border-gray-200 pt-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Chi tiết thanh toán</h3>
            <table class="w-full text-left">
                <tbody>
                    <tr>
                        <td class="py-2 text-gray-600">Tổng giá</td>
                        <td class="py-2 text-right font-medium">{{ number_format($booking->getOriginalPrice()) }} đ</td>
                    </tr>
                    @if($booking->discount_percent > 0)
                    <tr>
                        <td class="py-2 text-gray-600">Giảm giá ({{ $booking->discount_percent }}%)</td>
                        <td class="py-2 text-right font-medium text-red-600">-{{ number_format($booking->getDiscountAmount()) }} đ</td>
                    </tr>
                    @if($booking->promotion)
                    <tr>
                        <td class="py-2 text-gray-600">Mã khuyến mãi</td>
                        <td class="py-2 text-right font-medium">{{ $booking->promotion->code }}</td>
                    </tr>
                    @endif
                    @endif
                    <tr class="border-t border-gray-200">
                        <td class="py-2 text-lg font-bold">Tổng tiền</td>
                        <td class="py-2 text-right text-lg font-bold">{{ number_format($booking->total_price) }} đ</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($booking->status == 'cancelled' && $booking->refunds->count() > 0)
        <!-- Refund Details -->
        <div class="border-t border-gray-200 pt-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Thông tin hoàn tiền</h3>
            <table class="w-full text-left">
                <tbody>
                    <tr>
                        <td class="py-2 text-gray-600">Số tiền hoàn trả</td>
                        <td class="py-2 text-right font-medium">{{ number_format($booking->refunds->first()->refund_amount) }} Xu</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600">Lý do hoàn tiền</td>
                        <td class="py-2 text-right font-medium">{{ $booking->refunds->first()->refund_reason }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600">Thời gian hoàn tiền</td>
                        <td class="py-2 text-right font-medium">{{ \Carbon\Carbon::parse($booking->refunds->first()->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- QR Code -->
        <div class="border-t border-gray-200 pt-4 mb-6 flex justify-center">
            <div class="text-center">
                <img src="{{ asset('image/qrcode.png') }}" alt="QR Code" class="w-32 h-32 mx-auto">
                <p class="text-gray-600 mt-2">Quý khách vui lòng quét mã QR để thanh toán</p>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="border-t border-gray-200 pt-4">
            <p class="text-gray-600 text-center">Cảm ơn quý khách đã đến trải nghiệm sân cầu JBadminton.</p>
            <p class="text-gray-600 text-center">Hẹn gặp lại quý khách lần sau!</p>
            <p class="text-gray-500 text-center text-sm mt-2">Hóa đơn được in ngày: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>

</html>
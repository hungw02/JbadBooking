<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $rental->transaction_type === 'rent' ? 'hóa đơn cho thuê' : 'Hóa đơn bán hàng' }} #{{ $rental->id }}</title>
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
            <a href="{{ route('storage.index') }}" class="ml-3 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-md transition">
                Quay lại
            </a>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $rental->transaction_type === 'rent' ? 'hóa đơn cho thuê' : 'HÓA ĐƠN BÁN HÀNG' }}
                </h1>
                <p class="text-gray-600">Mã đơn: #{{ $rental->id }}</p>
                <p class="text-gray-600">Ngày tạo: {{ $rental->created_at->format('d/m/Y H:i') }}</p>
                <span class="inline-block mt-2 px-3 py-1 text-sm font-semibold rounded-full 
                    {{ $rental->transaction_type === 'sale' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $rental->transaction_type === 'sale' ? 'Bán hàng' : 'Cho thuê' }}
                </span>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-semibold text-gray-800">JBadminton</h2>
                <p class="text-gray-600">123 Đường ABC, Quận XYZ</p>
                <p class="text-gray-600">Hà Đông, Hà Nội</p>
                <p class="text-gray-600">0123 456 789</p>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Chi tiết giao dịch</h3>
            <div class="overflow-x-auto">
                <table class="w-full rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Số lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Đơn giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($rental->transaction_type === 'rent' || !str_contains($rental->product_id, ','))
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $rental->product_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $rental->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($rental->total_price / $rental->quantity) }} VNĐ</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($rental->total_price) }} VNĐ</td>
                        </tr>
                        @else
                            @php
                                $productIds = explode(',', $rental->product_id);
                                $productNames = explode(',', $rental->product_name);
                                $quantities = explode(',', $rental->quantity);
                                $subtotals = [];
                                
                                // Get the products to calculate unit prices
                                $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
                                
                                // Calculate subtotals and unit prices
                                $total = 0;
                                foreach ($productIds as $index => $productId) {
                                    $product = $products[$productId] ?? null;
                                    $quantity = $quantities[$index] ?? 0;
                                    $unitPrice = $product ? $product->selling_price : 0;
                                    $subtotal = $unitPrice * $quantity;
                                    $subtotals[] = $subtotal;
                                    $total += $subtotal;
                                }
                            @endphp
                            
                            @foreach($productIds as $index => $productId)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $productNames[$index] ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quantities[$index] ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($products[$productId]->selling_price ?? 0) }} VNĐ
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($subtotals[$index] ?? 0) }} VNĐ
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total and Status -->
        <div class="border-t border-gray-200 pt-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Trạng thái</h3>
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                        {{ $rental->status === 'not_returned' ? 'bg-red-100 text-red-800' : 
                          ($rental->status === 'returned' ? 'bg-green-100 text-green-800' : 
                          'bg-purple-100 text-purple-800') }}">
                        @if($rental->status === 'not_returned')
                        Chưa trả
                        @elseif($rental->status === 'returned')
                        Đã trả
                        @elseif($rental->status === 'completed')
                        Đã hoàn thành
                        @endif
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-gray-600">Tổng tiền:</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($rental->total_price) }} VNĐ</p>
                </div>
            </div>
        </div>

        <!-- Note Section -->
        @if($rental->note)
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Ghi chú</h3>
            <p class="text-gray-700">{{ $rental->note }}</p>
        </div>
        @endif

        <!-- Rental Rules (for racquet rentals only) -->
        @if($rental->transaction_type === 'rent')
        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Quy tắc khi bạn thuê vợt</h3>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
                <li>Khách hàng kiểm tra vợt trước khi nhận.</li>
                <li>Trả vợt cho cửa hàng khi trả sân.</li>
                <li>Vợt phải được trả trong tình trạng như khi thuê, không bị hư hỏng.</li>
                <li>Trường hợp làm mất hoặc hư hỏng vợt, khách hàng phải bồi thường 100% giá trị vợt.</li>
                <li>Nếu trả vợt muộn, phí phạt 10% giá thuê/ngày.</li>
                <li>Không được mang vợt ra khỏi sân.</li>
            </ul>
        </div>
        @endif

        <!-- QR Code -->
        <div class="border-t border-gray-200 pt-4 mb-6 flex justify-center">
            <div class="text-center">
                <img src="{{ asset('image/qrcode.png') }}" alt="QR Code" class="w-32 h-32 mx-auto">
                <p class="text-gray-600 mt-2">Quý khách vui lòng quét mã QR để thanh toán</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 pt-4 mt-6 text-center">
            <p class="text-gray-600">Cảm ơn quý khách đã đến trải nghiệm sân cầu JBadminton.</p>
            <p class="text-gray-600">Hẹn gặp lại quý khách lần sau!</p>
            <p class="text-gray-500 text-sm mt-2">Hóa đơn được in ngày: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>

</html>
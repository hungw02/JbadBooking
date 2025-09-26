@extends('layout.main-owner')

@section('title', $rental->transaction_type === 'rent' ? 'Chi tiết hóa đơn cho thuê' : 'Chi tiết hóa đơn bán hàng')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $rental->transaction_type === 'rent' ? 'Chi tiết hóa đơn cho thuê' : 'Chi tiết hóa đơn bán hàng' }} #{{ $rental->id }}
                    </h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('storage.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <a href="{{ route('storage.edit', $rental) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('storage.invoice', $rental) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                            <i class="fas fa-print"></i> In hóa đơn
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-white shadow overflow-hidden rounded-lg">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $rental->transaction_type === 'rent' ? 'Thông tin hóa đơn cho thuê' : 'Thông tin hóa đơn bán hàng' }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Chi tiết về {{ $rental->transaction_type === 'rent' ? 'hóa đơn cho thuê' : 'hóa đơn bán hàng' }} và trạng thái hiện tại.
                            </p>
                        </div>

                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Mã phiếu</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">#{{ $rental->id }}</dd>
                                </div>

                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Loại giao dịch</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $rental->transaction_type === 'sale' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $rental->transaction_type === 'sale' ? 'Bán hàng' : 'Cho thuê' }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Tên sản phẩm</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        @if($rental->transaction_type === 'rent' || !str_contains($rental->product_id, ','))
                                            {{ $rental->product_name }}
                                        @else
                                            @php
                                                $productNames = explode(',', $rental->product_name);
                                                $quantities = explode(',', $rental->quantity);
                                            @endphp
                                            <div class="space-y-2">
                                                @foreach($productNames as $index => $name)
                                                    <div>{{ $name }} ({{ $quantities[$index] ?? 0 }} sản phẩm)</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </dd>
                                </div>

                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Số lượng</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        @if($rental->transaction_type === 'rent' || !str_contains($rental->product_id, ','))
                                            {{ $rental->quantity }}
                                        @else
                                            @php
                                                $quantities = explode(',', $rental->quantity);
                                                $totalQuantity = array_sum($quantities);
                                            @endphp
                                            {{ $totalQuantity }} (tổng số sản phẩm)
                                        @endif
                                    </dd>
                                </div>

                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Tổng tiền</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($rental->total_price) }} VNĐ</dd>
                                </div>

                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        @php
                                            $statusClass = [
                                                'returned' => 'bg-green-100 text-green-800',
                                                'not_returned' => 'bg-red-100 text-red-800',
                                                'completed' => 'bg-purple-100 text-purple-800'
                                            ];
                                            
                                            $statusText = [
                                                'returned' => 'Đã trả',
                                                'not_returned' => 'Chưa trả',
                                                'completed' => 'Đã hoàn thành'
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass[$rental->status] }}">
                                            {{ $statusText[$rental->status] }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rental->created_at->format('d/m/Y H:i:s') }}</dd>
                                </div>

                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rental->updated_at->format('d/m/Y H:i:s') }}</dd>
                                </div>

                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Ghi chú</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rental->note ?: 'Không có ghi chú' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($rental->transaction_type === 'rent' && $rental->status === 'not_returned')
                        <div class="mt-6">
                            <form action="{{ route('storage.return', $rental) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200"
                                    onclick="return confirm('Xác nhận khách đã trả sản phẩm này?')">
                                    <i class="fas fa-undo"></i> Xác nhận trả đồ
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 
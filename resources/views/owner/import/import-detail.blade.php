@extends('layout.main-owner')

@section('title', 'Chi tiết nhập hàng')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Chi tiết đơn nhập hàng #{{ $import->id }}</h2>
                    <a href="{{ route('imports.index') }}" class="text-blue-600 hover:text-blue-800 transition duration-200">
                        &larr; Quay lại danh sách
                    </a>
                </div>

                <div class="p-6">
                    <div class="bg-white shadow overflow-hidden rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Thông tin đơn nhập hàng
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Ngày nhập: {{ $import->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Nhà cung cấp
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $import->workshop_name }}
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Tổng tiền
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ number_format($import->total_price) }} VNĐ
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Người nhập
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $import->owner->fullname ?? 'N/A' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-white shadow overflow-hidden rounded-lg">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Danh sách sản phẩm
                            </h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tên sản phẩm
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Số lượng
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Đơn giá nhập
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tổng tiền
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Thông tin hiện tại
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lịch sử
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($import->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $item->product->name ?? 'Sản phẩm đã bị xóa' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($item->import_price) }} VNĐ
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($item->total_price) }} VNĐ
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($item->product)
                                                    <div>Đơn giá nhập: {{ number_format($item->product->import_price) }} VNĐ</div>
                                                    <div>Số lượng: {{ $item->product->quantity }}</div>
                                                @else
                                                    <span class="text-red-500">Sản phẩm không còn tồn tại</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($item->product)
                                                    <a href="{{ route('product.import.history', $item->product_id) }}" class="text-blue-600 hover:text-blue-800">
                                                        Xem lịch sử
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">Không khả dụng</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('imports.destroy', $import) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa thông tin nhập hàng này?')">
                                Xóa thông tin nhập hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 
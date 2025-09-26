@extends('layout.main-owner')

@section('title', 'Quản lý cửa hàng')

@section('content')
<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Quản lý cửa hàng</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('storage.create', ['transaction_type' => 'sale']) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-cash-register mr-1"></i> Tạo hóa đơn bán hàng
                        </a>
                        <a href="{{ route('storage.create', ['transaction_type' => 'rent']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-exchange-alt mr-1"></i> Tạo hóa đơn cho thuê
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <div class="mb-6">
                    <form action="{{ route('storage.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                        <div class="w-48">
                            <label for="transaction_type" class="block text-sm font-medium text-gray-700">Loại giao dịch</label>
                            <select name="transaction_type" id="transaction_type" class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-blue-400 focus:border-blue-400">
                                <option value="">Tất cả</option>
                                <option value="sale" {{ request('transaction_type') === 'sale' ? 'selected' : '' }}>Bán hàng</option>
                                <option value="rent" {{ request('transaction_type') === 'rent' ? 'selected' : '' }}>Cho thuê</option>
                            </select>
                        </div>

                        <div class="w-48">
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" id="status" class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-blue-400 focus:border-blue-400">
                                <option value="">Tất cả</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Đã trả</option>
                                <option value="not_returned" {{ request('status') === 'not_returned' ? 'selected' : '' }}>Chưa trả</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                <i class="fas fa-filter"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden shadow">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Loại GD
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Tên sản phẩm
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Số lượng
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Tổng tiền
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Trạng thái
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Ngày tạo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">#{{ $transaction->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $transaction->transaction_type === 'sale' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $transaction->transaction_type === 'sale' ? 'Bán hàng' : 'Cho thuê' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($transaction->transaction_type === 'rent' || !str_contains($transaction->product_id, ','))
                                            {{ $transaction->product_name }}
                                        @else
                                            @php
                                                $productNames = explode(',', $transaction->product_name);
                                                $firstProduct = $productNames[0] ?? '';
                                                $additionalCount = count($productNames) - 1;
                                            @endphp
                                            {{ $firstProduct }} 
                                            @if($additionalCount > 0)
                                                <span class="text-xs text-gray-500">(+{{ $additionalCount }} sản phẩm khác)</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($transaction->transaction_type === 'rent' || !str_contains($transaction->product_id, ','))
                                            {{ $transaction->quantity }}
                                        @else
                                            @php
                                                $quantities = explode(',', $transaction->quantity);
                                                $totalQuantity = array_sum($quantities);
                                            @endphp
                                            {{ $totalQuantity }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($transaction->total_price) }} VNĐ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass[$transaction->status] }}">
                                        {{ $statusText[$transaction->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('storage.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>

                                        <a href="{{ route('storage.edit', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>

                                        @if($transaction->transaction_type === 'rent' && $transaction->status === 'not_returned')
                                        <form action="{{ route('storage.return', $transaction) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-600 hover:text-green-900"
                                                onclick="return confirm('Xác nhận khách đã trả sản phẩm này?')">
                                                <i class="fas fa-undo"></i> Trả đồ
                                            </button>
                                        </form>
                                        @endif

                                        <a href="{{ route('storage.invoice', $transaction) }}" class="text-purple-600 hover:text-purple-900">
                                            <i class="fas fa-print"></i> In hóa đơn
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Không có giao dịch nào
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
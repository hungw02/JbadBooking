@extends('layout.main-owner')

@section('title', $rental->transaction_type === 'rent' ? 'Cập nhật hóa đơn cho thuê' : 'Cập nhật hóa đơn bán hàng')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $rental->transaction_type === 'rent' ? 'Cập nhật hóa đơn cho thuê' : 'Cập nhật hóa đơn bán hàng' }} #{{ $rental->id }}
                    </h2>
                </div>

                <div class="p-6">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('storage.update', $rental) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Thông tin sản phẩm</label>
                            <div class="mt-2 p-3 bg-gray-100 rounded-lg">
                                <p class="font-medium">{{ $rental->product_name }}</p>
                                <p class="text-sm text-gray-600">Số lượng: {{ $rental->quantity }}</p>
                                <p class="text-sm text-gray-600">Tổng tiền: {{ number_format($rental->total_price) }} VNĐ</p>
                                <p class="text-sm text-gray-600">
                                    Loại giao dịch: 
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $rental->transaction_type === 'sale' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $rental->transaction_type === 'sale' ? 'Bán hàng' : 'Cho thuê' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" id="status" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">
                                @if($rental->transaction_type === 'rent')
                                    <option value="not_returned" {{ old('status', $rental->status) === 'not_returned' ? 'selected' : '' }}>Chưa trả</option>
                                    <option value="returned" {{ old('status', $rental->status) === 'returned' ? 'selected' : '' }}>Đã trả</option>
                                @else
                                    <option value="completed" {{ old('status', $rental->status) === 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                @endif
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                            <textarea name="note" id="note" rows="3" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">{{ old('note', $rental->note) }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('storage.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 
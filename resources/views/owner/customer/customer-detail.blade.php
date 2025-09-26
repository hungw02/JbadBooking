@extends('layout.main-owner')

@section('title', 'Chi tiết khách hàng')

@section('content')
<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-5 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Thông tin khách hàng</h2>
                    <a href="{{ route('customers.index') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                            <div class="mt-1 text-lg">{{ $customer->fullname }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                            <div class="mt-1 text-lg">{{ $customer->username }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1 text-lg">{{ $customer->email }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                            <div class="mt-1 text-lg">{{ $customer->phone }}</div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ví cá nhân</label>
                            <div class="mt-1 text-lg font-semibold text-blue-600">
                                {{ number_format($customer->wallets) }} Xu
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Điểm tích lũy</label>
                            <div class="mt-1 text-lg">{{ $customer->point }} điểm</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hạng</label>
                            <div class="mt-1">
                                <span class="text-sm rounded-full {{ $customer->rank_color }}">
                                    {{ $customer->rank }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <div class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $customer->status === 'active' ? 'Hoạt động' : 'Đã khóa' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

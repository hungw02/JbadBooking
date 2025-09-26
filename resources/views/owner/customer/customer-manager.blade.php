@extends('layout.main-owner')

@section('title', 'Quản lý khách hàng')

@section('content')
<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Quản lý khách hàng</h2>

                <!-- Tìm kiếm -->
                <form action="{{ route('customers.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Nhập tên khách hàng..."
                        class="px-4 py-2 border rounded-lg focus:ring-blue-400 focus:border-blue-400 outline-none"
                        value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>

            <!-- Thống kê rank -->
            <div class="grid grid-cols-7 gap-4 mb-6">
                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/no_rank.png') }}" alt="Chưa có rank" class="h-16">
                    </div>
                    <div class="font-bold text-gray-600">Chưa có</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['no_rank'] }}</div>
                </div>

                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/bronze.png') }}" alt="Đồng" class="h-16">
                    </div>
                    <div class="font-bold text-yellow-800">Đồng</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['bronze'] }}</div>
                </div>

                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/silver.png') }}" alt="Bạc" class="h-16">
                    </div>
                    <div class="font-bold text-gray-500">Bạc</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['silver'] }}</div>
                </div>

                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/gold.png') }}" alt="Vàng" class="h-16">
                    </div>
                    <div class="font-bold text-yellow-500">Vàng</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['gold'] }}</div>
                </div>

                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/platinum.png') }}" alt="Bạch kim" class="h-16">
                    </div>
                    <div class="font-bold text-blue-300">Bạch kim</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['platinum'] }}</div>
                </div>

                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/diamond.png') }}" alt="Kim cương" class="h-16">
                    </div>
                    <div class="font-bold text-blue-500">Kim cương</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['diamond'] }}</div>
                </div>

                <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-center mb-3">
                        <img src="{{ asset('image/rank/ruby.png') }}" alt="Ruby" class="h-16">
                    </div>
                    <div class="font-bold text-red-600">Ruby</div>
                    <div class="text-2xl font-semibold">{{ $rankStats['ruby'] }}</div>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Tên khách hàng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Thông tin liên hệ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Ví cá nhân
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Điểm tích lũy
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Hạng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $customer->fullname }}
                            </td>
                            <td class="px-6 py-4">
                                <div>Email: {{ $customer->email }}</div>
                                <div>SĐT: {{ $customer->phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($customer->wallets) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $customer->point }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ asset('image/rank/' . ($customer->rank_image ?? 'no_rank') . '.png') }}" alt="{{ $customer->rank }}" class="h-6 mr-2">
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $customer->status === 'active' ? 'Hoạt động' : 'Đã khóa' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('customers.show', $customer) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </a>
                                    <form action="{{ route('customers.toggle-status', $customer) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="{{ $customer->status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                            onclick="return confirm('Bạn có chắc chắn muốn {{ $customer->status === 'active' ? 'khóa' : 'mở khóa' }} tài khoản này?')">
                                            <i class="fas {{ $customer->status === 'active' ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                            {{ $customer->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layout.main-customer')

@section('title', 'Đổi lịch đặt định kỳ')

@section('content')
<div class="max-w-7xl mx-auto p-5">
    <div class="mb-4 flex items-center">
        <a href="{{ route('booking.subscription.detail', $booking->id) }}" class="text-blue-600 hover:underline flex items-center">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
        </a>
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 p-4">
            <h1 class="text-white text-xl font-bold">Đổi sân - Lịch đặt định kỳ #{{ $booking->id }}</h1>
        </div>

        <div class="p-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 mb-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-4 pb-2 border-b border-blue-200">
                    <i class="fa-solid fa-info-circle mr-2"></i>Thông tin lịch đặt hiện tại
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col">
                        <span class="text-gray-600">Sân</span>
                        <span class="font-medium text-lg">{{ $booking->court->name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">Ngày trong tuần</span>
                        <span class="font-medium text-lg">
                            {{ App\Models\CourtRate::getDayNameStatic($booking->day_of_week) }}
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">Thời gian</span>
                        <span class="font-medium text-lg">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="flex flex-col">
                        <span class="text-gray-600">Từ ngày</span>
                        <span class="font-medium text-lg">{{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-600">Đến ngày</span>
                        <span class="font-medium text-lg">{{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            @if(count($availableCourts) > 0)
                <form action="{{ route('booking.subscription.change.submit', $booking->id) }}" method="POST">
                    @csrf
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fa-solid fa-list-check mr-2"></i>Chọn sân mới
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        @foreach($availableCourts as $court)
                        <div class="bg-white border rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            @if(isset($court['image']) && $court['image'])
                                <img src="{{ asset($court['image']) }}" alt="{{ $court['name'] }}" class="w-20 h-20 object-cover rounded mb-2">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-2">{{ $court['name'] }}</h3>
                                <div class="flex items-center mt-auto">
                                    <input type="radio" id="court_{{ $court['id'] }}" name="court_id" value="{{ $court['id'] }}" required
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <label for="court_{{ $court['id'] }}" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer">
                                        Chọn sân này
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white rounded transition">
                            <i class="fa-solid fa-check-circle mr-2"></i> Xác nhận đổi sân
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fa-solid fa-triangle-exclamation mr-3 mt-1"></i>
                        <div>
                            <p class="font-semibold">Không tìm thấy sân trống</p>
                            <p>Không có sân nào khác còn trống trong khung giờ này. Vui lòng thử lại sau hoặc liên hệ với quản lý sân.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <a href="{{ route('booking.subscription.detail', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
            <i class="fa-solid fa-circle-info mr-2"></i>Lưu ý khi đổi sân định kỳ
        </h2>
        <div class="bg-white p-4 rounded-lg shadow">
            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                <li>Việc đổi sân sẽ áp dụng cho tất cả các buổi tập còn lại trong gói đặt sân định kỳ.</li>
                <li>Nếu sân mới có giá khác với sân cũ, chúng tôi sẽ liên hệ để xác nhận phần chênh lệch.</li>
                <li>Bạn chỉ có thể đổi sân tối đa 2 lần cho mỗi gói định kỳ.</li>
                <li>Thay đổi sẽ có hiệu lực từ buổi tập tiếp theo.</li>
                <li>Nếu bạn muốn thay đổi ngày hoặc giờ, vui lòng liên hệ trực tiếp với quản lý sân.</li>
            </ul>
        </div>
    </div>
</div>
@endsection 
@extends('layout.main-customer')

@section('title', 'Quản lý lịch đặt')

@section('content')
<div class="max-w-7xl mx-auto rounded-xl m-10 py-8 px-4 sm:px-6 lg:px-8 bg-gray-900/80 text-white">
    <!-- Tabs -->
    <div class="border-b border-gray-700 mb-8">
        <div class="flex space-x-8">
            <button id="tab-single" class="py-4 px-1 border-b-2 border-transparent font-medium text-cyan-400 focus:outline-none flex items-center space-x-2" aria-current="page">
                <i class="fa-solid fa-calendar-day"></i>
                <span>Lịch đặt theo buổi</span>
            </button>
            <button id="tab-subscription" class="py-4 px-1 border-b-2 border-transparent font-medium text-cyan-400 focus:outline-none flex items-center space-x-2">
                <i class="fa-solid fa-calendar-week"></i>
                <span>Lịch đặt định kỳ</span>
            </button>
        </div>
    </div>

    <!-- Tab đặt theo buổi -->
    <div id="content-single" class="tab-content">
        <!-- Filters -->
        <div class="flex items-center mb-6 flex-wrap gap-3">
            <span class="text-gray-300 font-medium mr-2">Xem theo trạng thái:</span>
            <button data-status="all" data-type="single" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Tất cả</button>
            <button data-status="confirmed" data-type="single" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Đã xác nhận</button>
            <button data-status="completed" data-type="single" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Đã hoàn thành</button>
            <button data-status="cancelled" data-type="single" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Đã hủy</button>
        </div>

        <!-- Booking Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($singleBookings) && $singleBookings->count() > 0)
            @foreach($singleBookings as $booking)
            <div class="booking-row single-booking bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-700" data-status="{{ $booking->status }}">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-lg font-semibold text-white">Mã đơn: {{ $booking->id }}</span>
                        @if($booking->status == 'confirmed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-900 text-emerald-300 border border-emerald-600">Đã xác nhận</span>
                        @elseif($booking->status == 'completed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-900 text-blue-300 border border-blue-600">Đã hoàn thành</span>
                        @elseif($booking->status == 'cancelled')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-rose-900 text-rose-300 border border-rose-600">Đã hủy</span>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fa-solid fa-volleyball text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Sân: {{ $booking->court->name }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-calendar text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Ngày chơi: {{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-clock text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Giờ chơi: {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-money-bill text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="font-medium text-white">Tổng tiền: {{ number_format($booking->total_price) }} đ</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 px-5 py-3 flex justify-end space-x-3">
                    <a href="{{ route('booking.detail', $booking->id) }}" class="inline-flex items-center justify-center rounded-md bg-cyan-900 px-3 py-2 text-sm font-medium text-cyan-300 hover:bg-cyan-800 border border-cyan-700">
                        <i class="fa-solid fa-eye mr-1"></i> Chi tiết
                    </a>

                    @if($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->start_time) > now())
                    <a href="{{ route('booking.change', $booking->id) }}" class="inline-flex items-center justify-center rounded-md bg-amber-900 px-3 py-2 text-sm font-medium text-amber-300 hover:bg-amber-800 border border-amber-700">
                        <i class="fa-solid fa-exchange-alt mr-1"></i> Đổi
                    </a>

                    @if(Auth::user()->role == 'owner')
                    <a href="{{ route('owner.bookings.single', $booking->id) }}" class="inline-flex items-center justify-center rounded-md bg-rose-900 px-3 py-2 text-sm font-medium text-rose-300 hover:bg-rose-800 border border-rose-700">
                        <i class="fa-solid fa-times-circle mr-1"></i> Hủy
                    </a>
                    @else
                    <form action="{{ route('booking.single.cancel', $booking->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt sân này?')">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-rose-900 px-3 py-2 text-sm font-medium text-rose-300 hover:bg-rose-800 border border-rose-700">
                            <i class="fa-solid fa-times-circle mr-1"></i> Hủy
                        </button>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
            @endforeach
            @else
            <div class="col-span-full py-16 flex flex-col items-center justify-center bg-gray-800 rounded-xl shadow-sm border border-gray-700">
                <div class="text-gray-500 mb-4">
                    <i class="fa-solid fa-calendar-xmark text-5xl"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-300">Bạn chưa có lịch đặt sân nào</h3>
                <p class="text-gray-500 mt-2">Hãy đặt sân để bắt đầu chơi ngay</p>
                <a href="{{ route('booking.single.store') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-900 px-3 py-2 text-sm font-medium text-cyan-300 hover:bg-cyan-800 border border-cyan-700">
                    <i class="fa-solid fa-plus mr-1"></i> Đặt sân
                </a>
            </div>
            @endif
        </div>

        <!-- Phân trang -->
        @if($singleBookings->hasPages())
        <div class="mt-6 bg-gray-800 rounded-xl p-4 border border-gray-700">
            <x-pagination-dark :paginator="$singleBookings" />
        </div>
        @endif
    </div>

    <!-- Tab đặt định kỳ -->
    <div id="content-subscription" class="tab-content hidden">
        <!-- Filters -->
        <div class="flex items-center mb-6 flex-wrap gap-3">
            <span class="text-gray-300 font-medium mr-2">Xem theo trạng thái:</span>
            <button data-status="all" data-type="subscription" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Tất cả</button>
            <button data-status="confirmed" data-type="subscription" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Đã xác nhận</button>
            <button data-status="completed" data-type="subscription" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Đã hoàn thành</button>
            <button data-status="cancelled" data-type="subscription" class="status-filter-single px-4 py-2 rounded-lg text-sm font-medium bg-cyan-900 text-cyan-300 shadow-sm hover:bg-cyan-800 hover:text-white transition duration-150 border border-cyan-500">Đã hủy</button>
        </div>

        <!-- Booking Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($subscriptionBookings) && $subscriptionBookings->count() > 0)
            @foreach($subscriptionBookings as $booking)
            <div class="booking-row subscription-booking bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-700" data-status="{{ $booking->status }}">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-lg font-semibold text-white">Mã đơn: {{ $booking->id }}</span>
                        @if($booking->status == 'confirmed')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-cyan-900 text-cyan-300 border border-cyan-700">Đã xác nhận</span>
                        @elseif($booking->status == 'cancelled')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-rose-900 text-rose-300 border border-rose-700">Đã hủy</span>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fa-solid fa-volleyball text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Sân: {{ $booking->court->name }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-calendar-week text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Ngày chơi: {{ App\Models\CourtRate::getDayNameStatic($booking->day_of_week) }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-clock text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Giờ chơi: {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-calendar-day text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="text-gray-300">Ngày đặt: {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-solid fa-money-bill text-cyan-400 w-5 text-center mr-3"></i>
                            <span class="font-medium text-white">Tổng tiền: {{ number_format($booking->total_price) }} đ</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 px-5 py-3 flex justify-end space-x-3">
                    <a href="{{ route('booking.subscription.detail', $booking->id) }}" class="inline-flex items-center justify-center rounded-md bg-cyan-900 px-3 py-2 text-sm font-medium text-cyan-300 hover:bg-cyan-800 border border-cyan-700">
                        <i class="fa-solid fa-eye mr-1"></i> Chi tiết
                    </a>

                    @if($booking->status == 'confirmed' && \Carbon\Carbon::parse($booking->end_date) > now())
                    <a href="{{ route('booking.subscription.change', $booking->id) }}" class="inline-flex items-center justify-center rounded-md bg-amber-900 px-3 py-2 text-sm font-medium text-amber-300 hover:bg-amber-800 border border-amber-700">
                        <i class="fa-solid fa-exchange-alt mr-1"></i> Đổi
                    </a>

                    @if(Auth::user()->role == 'owner')
                    <a href="{{ route('owner.bookings.subscription', $booking->id) }}" class="inline-flex items-center justify-center rounded-md bg-rose-900 px-3 py-2 text-sm font-medium text-rose-300 hover:bg-rose-800 border border-rose-700">
                        <i class="fa-solid fa-times-circle mr-1"></i> Hủy
                    </a>
                    @else
                    <form action="{{ route('booking.subscription.cancel', $booking) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt sân định kỳ này?')">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-rose-900 px-3 py-2 text-sm font-medium text-rose-300 hover:bg-rose-800 border border-rose-700">
                            <i class="fa-solid fa-times-circle mr-1"></i> Hủy
                        </button>
                    </form>
                    @endif
                    @endif
                </div>
            </div>
            @endforeach
            @else
            <div class="col-span-full py-16 flex flex-col items-center justify-center bg-gray-800 rounded-xl shadow-sm border border-gray-700">
                <div class="text-gray-500 mb-4">
                    <i class="fa-solid fa-calendar-xmark text-5xl"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-300">Bạn chưa có lịch đặt sân nào</h3>
                <p class="text-gray-500 mt-2">Hãy đặt sân để bắt đầu chơi ngay</p>
                <a href="{{ route('booking.subscription.store') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-900 px-3 py-2 text-sm font-medium text-cyan-300 hover:bg-cyan-800 border border-cyan-700">
                    <i class="fa-solid fa-plus mr-1"></i> Đặt sân
                </a>
            </div>
            @endif
        </div>

        <!-- Phân trang -->
        @if($subscriptionBookings->hasPages())
        <div class="mt-6 bg-gray-800 rounded-xl p-4 border border-gray-700">
            <x-pagination-dark :paginator="$subscriptionBookings" />
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý chuyển tab
        const tabSingle = document.getElementById('tab-single');
        const tabSubscription = document.getElementById('tab-subscription');
        const contentSingle = document.getElementById('content-single');
        const contentSubscription = document.getElementById('content-subscription');

        tabSingle.addEventListener('click', function() {
            // Active tab styling
            tabSingle.classList.add('border-cyan-400');
            tabSubscription.classList.remove('border-cyan-400');

            // Show/hide content
            contentSingle.classList.remove('hidden');
            contentSubscription.classList.add('hidden');
        });

        tabSubscription.addEventListener('click', function() {
            // Active tab styling
            tabSingle.classList.remove('border-cyan-400');
            tabSubscription.classList.add('border-cyan-400');

            // Show/hide content
            contentSingle.classList.add('hidden');
            contentSubscription.classList.remove('hidden');
        });

        // Xử lý lọc theo trạng thái - Single bookings
        const statusFiltersSingle = document.querySelectorAll('.status-filter-single');
        statusFiltersSingle.forEach(filter => {
            filter.addEventListener('click', function() {
                const status = this.dataset.status;

                // Đánh dấu nút đang active
                statusFiltersSingle.forEach(btn => btn.classList.remove('bg-cyan-800', 'text-white'));
                this.classList.add('bg-cyan-800', 'text-white');

                // Lọc các card đơn đặt
                const bookingRows = document.querySelectorAll('.single-booking');
                bookingRows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Xử lý lọc theo trạng thái - Subscription bookings
        const statusFiltersSubscription = document.querySelectorAll('.status-filter-subscription');
        statusFiltersSubscription.forEach(filter => {
            filter.addEventListener('click', function() {
                const status = this.dataset.status;

                // Đánh dấu nút đang active
                statusFiltersSubscription.forEach(btn => btn.classList.remove('bg-cyan-800', 'text-white'));
                this.classList.add('bg-cyan-800', 'text-white');

                // Lọc các card đơn đặt
                const bookingRows = document.querySelectorAll('.subscription-booking');
                bookingRows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Đặt tab mặc định là active
        tabSingle.classList.add('border-cyan-400');
        statusFiltersSingle[0].classList.add('bg-cyan-800', 'text-white');
        statusFiltersSubscription[0].classList.add('bg-cyan-800', 'text-white');
    });
</script>
@endsection

@extends('layout.main-customer')

@section('title', 'Đặt sân định kỳ')

@section('content')
<div class="max-w-7xl mx-auto p-5">
    <h1 class="text-center text-2xl font-bold text-white mb-8 bg-blue-600 p-3 rounded-lg">Đặt sân định kỳ</h1>

    @if(session('error'))
    <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded mb-5">
        {{ session('error') }}
    </div>
    @endif

    <!-- Form thay đổi sân khi bị trùng -->
    @if(session('conflicts'))
    <div class="bg-yellow-100 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-5">
        <h4 class="font-bold">Tiếc quá, thông tin đặt sân của bạn đã bị trùng</h4>
        <div>{!! session('conflicts') !!}</div>

        @if(session('has_alternatives') && !empty(session('available_courts')))
        <div class="mt-4 bg-white p-4 rounded shadow-sm">
            <p class="mb-2 font-semibold">Các sân còn trống vào {{ session('conflict_day') }} {{ session('conflict_time') }}:</p>

            <form action="{{ route('booking.subscription.store') }}" method="POST" class="mt-2" id="alternativeCourtForm">
                @csrf
                <input type="hidden" name="day_of_week" value="{{ old('day_of_week', session('day_of_week')) }}">
                <input type="hidden" name="start_date" value="{{ old('start_date', session('start_date')) }}">
                <input type="hidden" name="end_date" value="{{ old('end_date', session('end_date')) }}">
                <input type="hidden" name="start_time" value="{{ old('start_time', session('start_time')) }}">
                <input type="hidden" name="end_time" value="{{ old('end_time', session('end_time')) }}">
                <input type="hidden" name="payment_type" value="{{ old('payment_type', session('payment_type', 'full')) }}">
                <input type="hidden" name="payment_method" value="{{ old('payment_method', session('payment_method', 'vnpay')) }}">
                <input type="hidden" name="confirm_alternatives" value="true">

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3">
                    @foreach(session('available_courts') as $court)
                    <div class="p-3 border rounded-lg hover:border-blue-500 transition-colors cursor-pointer court-option" data-court-id="{{ $court['id'] }}">
                        <div class="flex flex-col items-center">
                            <img src="{{ asset($court['image']) }}" alt="Sân {{ $court['name'] }}" class="w-20 h-20 object-cover rounded mb-2">
                            <div class="text-center">
                                <div class="text-sm font-semibold">Sân {{ $court['name'] }}</div>
                                <div class="flex items-center justify-center mt-2">
                                    <input type="checkbox" name="court_ids[]" value="{{ $court['id'] }}" class="alternative-court-select" id="court_{{ $court['id'] }}">
                                    <label for="court_{{ $court['id'] }}" class="ml-2 text-sm">Chọn sân này</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mt-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center" id="submit-alternatives">
                        <i class="fa-solid fa-check mr-2"></i>Đồng ý
                    </button>
                    <span class="text-sm text-gray-600">hoặc điều chỉnh thời gian và chọn sân khác bên dưới</span>
                </div>
            </form>

            <script>
                // Thêm script để xử lý việc chọn sân
                document.addEventListener('DOMContentLoaded', function() {
                    // Lấy các sân đã bị trùng lịch
                    const conflictingCourts = <?php echo json_encode(session('conflicting_courts', [])); ?>;

                    // Xử lý khi click vào một ô sân
                    const courtOptions = document.querySelectorAll('.court-option');
                    courtOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            const courtId = this.getAttribute('data-court-id');
                            const checkbox = this.querySelector(`input[value="${courtId}"]`);
                            checkbox.checked = !checkbox.checked;
                        });
                    });

                    // Kiểm tra trước khi submit
                    const alternativeForm = document.getElementById('alternativeCourtForm');
                    if (alternativeForm) {
                        alternativeForm.addEventListener('submit', function(e) {
                            const selectedCourts = document.querySelectorAll('.alternative-court-select:checked');
                            if (selectedCourts.length === 0) {
                                e.preventDefault();
                                alert('Vui lòng chọn ít nhất một sân trước khi tiếp tục');
                            }
                        });
                    }
                });
            </script>
        </div>
        @else
        <p class="mt-2">Không có sân nào còn trống trong khung giờ này. Vui lòng điều chỉnh thời gian hoặc chọn sân khác.</p>
        @endif
    </div>
    @endif

    <form id="subscriptionBookingForm" action="{{ route('booking.subscription.store') }}" method="POST">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Thông tin đặt sân định kỳ -->
        <div class="bg-gray-50 p-5 rounded-lg shadow-sm mb-8">
            <h2 class="text-xl text-blue-600 pb-3 border-b border-gray-200">Thông tin đặt sân định kỳ</h2>

            <div class="mt-4 bg-blue-50 p-4 rounded-lg border border-blue-100">
                <div class="flex items-start">
                    <div class="text-blue-600 text-2xl mr-3">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div>
                        <p class="mb-2 text-gray-700">Đặt sân định kỳ giúp bạn đảm bảo lịch chơi định kỳ và tiết kiệm chi phí với ưu đãi giảm 10% so với đặt theo buổi.</p>
                        <ul class="list-disc pl-5 text-gray-700 space-y-1">
                            <li>Chọn một ngày trong tuần định kỳ để đặt (Thứ 2 - Chủ nhật)</li>
                            <li>Đặt từ 4 tuần trở lên để được hưởng ưu đãi tặng nước</li>
                            <li>Chính sách hủy linh hoạt theo số buổi còn lại</li>
                            <li>Khi sân chính bị trùng, hệ thống sẽ tự động chuyển sang sân thay thế</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chọn thời gian bạn muốn chơi -->
        <div class="bg-gray-50 p-5 rounded-lg shadow-sm mb-8">
            <h2 class="text-xl text-blue-600 pb-3 border-b border-gray-200">1. Chọn thời gian bạn muốn chơi</h2>
            <!-- Ngày -->
            <div class="mt-6 flex gap-5 items-center">
                <label for="day-of-week" class="w-1/8 block font-semibold">Ngày trong tuần:</label>
                <select id="day-of-week" name="day_of_week" class="w-1/4 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none time-select" required>
                    <!-- <option value="">Chọn ngày trong tuần</option> -->
                    <option value="2" {{ old('day_of_week', session('day_of_week')) == 2 ? 'selected' : '' }}>Thứ 2</option>
                    <option value="3" {{ old('day_of_week', session('day_of_week')) == 3 ? 'selected' : '' }}>Thứ 3</option>
                    <option value="4" {{ old('day_of_week', session('day_of_week')) == 4 ? 'selected' : '' }}>Thứ 4</option>
                    <option value="5" {{ old('day_of_week', session('day_of_week')) == 5 ? 'selected' : '' }}>Thứ 5</option>
                    <option value="6" {{ old('day_of_week', session('day_of_week')) == 6 ? 'selected' : '' }}>Thứ 6</option>
                    <option value="7" {{ old('day_of_week', session('day_of_week')) == 7 ? 'selected' : '' }}>Thứ 7</option>
                    <option value="8" {{ old('day_of_week', session('day_of_week')) == 8 ? 'selected' : '' }}>Chủ nhật</option>
                </select>
            </div>
            <!-- Ngày -->
            <div class="mt-6 flex gap-5 items-center">
                <div class="w-1/2 flex items-center">
                    <label for="start-date" class="w-1/4 block font-semibold">Ngày bắt đầu:</label>
                    <input type="date" id="start-date" name="start_date" min="{{ date('Y-m-d') }}"
                        value="{{ old('start_date', session('start_date', date('Y-m-d'))) }}"
                        class="w-1/2 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none" required>
                </div>

                <div class="w-1/2 flex items-center">
                    <label for="end-date" class="w-1/4 block font-semibold">Ngày kết thúc:</label>
                    <input type="date" id="end-date" name="end_date"
                        min="{{ date('Y-m-d', strtotime('+28 days')) }}"
                        value="{{ old('end_date', session('end_date', date('Y-m-d', strtotime('+28 days')))) }}"
                        class="w-1/2 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none" required>
                </div>
            </div>
            <!-- Buổi -->
            <div class="mt-6 flex-col items-center">
                <p class="text-gray-600 italic text-sm">Khoảng thời gian tối thiểu là 4 tuần (28 ngày)</p>
                <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <i class="fa-solid fa-calendar-check text-blue-500 mr-2"></i>
                        <span class="font-semibold">Tổng số buổi: <span id="session-count" class="text-blue-700">0</span></span>
                    </div>
                </div>
            </div>
            <!-- Giờ chơi -->
            <div class="mt-6 flex gap-5 items-center">
                <div class="w-1/2 flex items-center">
                    <label for="start-time" class="w-1/4 block font-semibold">Giờ bắt đầu:</label>
                    <select id="start-time" name="start_time" required
                        class="w-1/2 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none time-select">
                        @foreach(range(5, 23) as $hour)
                        @foreach([0, 30] as $minute)
                        <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}"
                            {{ old('start_time', session('start_time')) == sprintf('%02d:%02d', $hour, $minute) ? 'selected' : '' }}>
                            {{ sprintf('%02d:%02d', $hour, $minute) }}
                        </option>
                        @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="w-1/2 flex items-center">
                    <label for="end-time" class="w-1/4 block font-semibold">Giờ kết thúc:</label>
                    <select id="end-time" name="end_time" required
                        class="w-1/2 p-2.5 border border-gray-300 rounded-md focus:border-blue-400 focus:outline-none time-select">
                        @foreach(range(6, 23) as $hour)
                        @foreach([0, 30] as $minute)
                        <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}"
                            {{ old('end_time', session('end_time')) == sprintf('%02d:%02d', $hour, $minute) ? 'selected' : '' }}>
                            {{ sprintf('%02d:%02d', $hour, $minute) }}
                        </option>
                        @endforeach
                        @endforeach
                        <option value="00:00" {{ old('end_time', session('end_time')) == '00:00' ? 'selected' : '' }}>00:00</option>
                    </select>
                </div>
            </div>
            <div id="time-error" class="text-red-500 text-sm mt-2"></div>
            <!-- Thông báo giá dự kiến -->
            <div id="price-preview" class="hidden my-5 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <i class="fa-solid fa-clock text-blue-500 mr-2"></i>
                    <span class="font-semibold">Thời gian: <span id="preview-hours" class="text-blue-700">0 giờ</span>/buổi</span>
                </div>
                <div class="flex items-center mt-2">
                    <i class="fa-solid fa-money-bill-wave text-blue-500 mr-2"></i>
                    <span class="font-semibold">Giá: <span id="preview-price" class="text-blue-700">0 đ</span>/sân/buổi</span>
                </div>
                <div class="w-2/5 mt-3 border-t border-blue-200 pt-2 text-sm text-gray-700">
                    <div class="mt-1 mb-2 flex items-center">
                        <i class="fa-solid fa-calculator text-blue-500 mr-2"></i>
                        <span>Chi phí dự tính:</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-left text-sm">
                            <tbody>
                                <tr>
                                    <td class="pr-2">•</td>
                                    <td>Đơn giá x Số buổi x Số sân:</td>
                                    <td class="text-right font-medium">
                                        <span id="preview-session-price">0 đ</span> x
                                        <span id="preview-session-count">0</span> x
                                        <span id="preview-court-count">0</span>
                                    </td>
                                    <td class="text-right font-medium" id="preview-subtotal">= 0 đ</td>
                                </tr>
                                <tr>
                                    <td class="pr-2">•</td>
                                    <td>Giảm giá:</td>
                                    <td></td>
                                    <td class="text-right font-medium text-green-600" id="preview-discount">0</td>
                                </tr>
                                <tr class="font-semibold">
                                    <td class="pr-2 align-top">•</td>
                                    <td colspan="2">Thành tiền:</td>
                                    <td class="text-right text-blue-700" id="preview-total">0 đ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Chọn sân -->
            <div class="mt-6">
                <label class="w-1/4 block font-semibold">Chọn sân:</label>
                <div class="flex gap-10 justify-center">
                    @foreach($courts as $court)
                    <label class="flex-col items-center court-select-container">
                        <img src="{{ asset($court->image) }}" alt="Sân {{ $court->name }}" class="w-24 object-cover rounded mb-2">
                        <div class="flex items-center gap-1">
                            <div class="text-sm font-semibold">Sân {{ $court->name }}</div>
                            <input type="checkbox" name="court_ids[]" value="{{ $court->id }}" class="court-select"
                                {{ in_array($court->id, old('court_ids', [])) ? 'checked' : '' }}>
                            <span class="checkbox-custom"></span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kiểm tra thông tin đặt sân -->
        <div class="bg-blue-50 p-5 rounded-lg shadow-sm mb-8">
            <h2 class="text-xl text-blue-600 mb-4">2. Kiểm tra thông tin đặt sân</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Ngày:</span>
                    <span id="dayDisplay" class="font-semibold">Chưa chọn</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Thời gian:</span>
                    <span id="timeDisplay" class="font-semibold">Chưa chọn</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Sân đánh:</span>
                    <span id="courtsDisplay" class="font-semibold">Chưa chọn</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Số buổi:</span>
                    <span id="sessionsDisplay" class="font-semibold">0 buổi</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Giá/buổi:</span>
                    <span id="pricePerSessionDisplay" class="font-semibold">0 VNĐ</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Tổng giá:</span>
                    <span id="totalPriceDisplay" class="font-semibold">0 VNĐ</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <span>Khuyến mãi:</span>
                    <span id="promotion-name" class="font-semibold text-green-600">Không có</span>
                </div>
                <div class="flex justify-between p-3 bg-white rounded">
                    <div class="flex justify-between items-center">
                        <div id="original-price-display" class="text-sm text-gray-600 hidden"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div id="discount-display" class="text-sm text-green-600 font-medium hidden"></div>
                    </div>
                </div>
                <div class="flex justify-between p-3 bg-green-100 rounded font-semibold col-span-full">
                    <span>Số tiền thanh toán:</span>
                    <span id="payment-amount">0</span>
                </div>
                <div id="court-error" class="text-red-500 text-sm text-center col-span-full"></div>
            </div>
        </div>

        <input type="hidden" id="promotion-select" name="promotion_id" value="">

        <!-- Phương thức thanh toán -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg shadow-md mb-8 border border-blue-100">
            <h2 class="text-xl text-blue-600 mb-4">3. Chọn phương thức thanh toán</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Loại thanh toán -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <label class="block font-semibold mb-4 text-gray-700 flex items-center">
                        <i class="fa-solid fa-wallet text-blue-500 mr-2"></i>
                        Loại thanh toán
                    </label>
                    <div class="space-y-3 pl-2">
                        <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                            <input type="radio" name="payment_type" value="deposit" required
                                {{ old('payment_type', '') == 'deposit' ? 'checked' : '' }}
                                class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="font-medium">Đặt cọc</span>
                                <p class="text-sm text-gray-500">Thanh toán 50% trước, phần còn lại khi đến sân</p>
                            </div>
                        </label>
                        <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                            <input type="radio" name="payment_type" value="full" required
                                {{ old('payment_type', 'full') == 'full' ? 'checked' : '' }}
                                class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500" checked>
                            <div>
                                <span class="font-medium">Thanh toán toàn bộ</span>
                                <p class="text-sm text-gray-500">Thanh toán 100% số tiền đặt sân</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <label class="block font-semibold mb-4 text-gray-700 flex items-center">
                        <i class="fa-solid fa-credit-card text-blue-500 mr-2"></i>
                        Phương thức thanh toán
                    </label>
                    <div class="space-y-3 pl-2">
                        <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                            <input type="radio" name="payment_method" value="vnpay" required
                                {{ old('payment_method', 'vnpay') == 'vnpay' ? 'checked' : '' }}
                                class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500" id="payment_method_vnpay" checked>
                            <div>
                                <div class="flex items-center">
                                    <span class="font-medium">VNPay</span>
                                    <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR-1024x800.png" alt="VNPay" class="h-6 ml-2">
                                </div>
                                <p class="text-sm text-gray-500">Thanh toán tiện lợi qua VNPay</p>
                            </div>
                        </label>
                        <label class="flex items-center p-2 rounded-md hover:bg-blue-50 transition-colors cursor-pointer">
                            <input type="radio" name="payment_method" value="wallet" required
                                {{ old('payment_method', '') == 'wallet' ? 'checked' : '' }}
                                class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500" id="payment_method_wallet">
                            <div>
                                <div class="flex items-center">
                                    <span class="font-medium">Ví cá nhân</span>
                                    <span class="text-sm bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full ml-2">
                                        Số dư: <span id="wallet-balance" class="font-semibold">{{ number_format(auth()->user()->wallets ?? 0) }} Xu</span>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">Thanh toán nhanh chóng từ số dư ví</p>
                            </div>
                        </label>
                        <div id="wallet-warning" class="text-red-600 text-sm hidden mt-2 p-3 bg-red-50 rounded-md border-l-4 border-red-500">
                            <div class="flex">
                                <i class="fa-solid fa-circle-exclamation mr-2 mt-0.5"></i>
                                <span>Số dư ví không đủ để thực hiện giao dịch này</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="user-wallet-balance" value="{{ auth()->user()->wallets ?? 0 }}">
        </div>

        <!-- Xác nhận -->
        <div class="flex justify-center gap-4 mt-8">
            <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-colors shadow-md flex items-center" id="submit-booking">
                <i class="fa-solid fa-check-circle mr-2"></i> Xác nhận đặt sân
            </button>
            <a href="{{ route('booking.index') }}" class="bg-gray-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors shadow-md flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
    </form>
</div>

<!-- Modal xác nhận thay đổi sân -->
<div id="courtChangeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Xác nhận thay đổi sân</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Chúng tôi phát hiện có lịch trùng với sân bạn đã chọn. Hệ thống sẽ tự động chuyển sang sân thay thế phù hợp.
                </p>
                <div id="courtChangeDetails" class="mt-4 text-left">
                    <!-- Details will be populated by JavaScript -->
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmCourtChange" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
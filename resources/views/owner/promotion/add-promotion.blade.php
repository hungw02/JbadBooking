@extends('layout.main-owner')

@section('title', 'Thêm khuyến mãi mới')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">Thêm khuyến mãi mới</h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('promotions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên khuyến mãi</label>
                            <input type="text" name="name" id="name"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Hình ảnh</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_percent" class="block text-sm font-medium text-gray-700">Phần trăm giảm giá (0% cho nội dung quảng bá)</label>
                            <input type="number" name="discount_percent" id="discount_percent"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('discount_percent', 0) }}" required min="0" max="100">
                            @error('discount_percent')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center mb-2">
                                <input type="checkbox" name="is_permanent_checkbox" id="is_permanent_checkbox" 
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    {{ old('is_permanent_checkbox') ? 'checked' : '' }}
                                    onchange="toggleEndDateField()">
                                <label for="is_permanent_checkbox" class="ml-2 block text-sm font-medium text-gray-700">
                                    Khuyến mãi vĩnh viễn
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="end_date_container">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
                                <input type="date" name="end_date" id="end_date"
                                    class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                    value="{{ old('end_date') }}">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" id="status"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Không kích hoạt</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="booking_type" class="block text-sm font-medium text-gray-700">Áp dụng cho loại đặt lịch</label>
                            <select name="booking_type" id="booking_type"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">
                                <option value="all" {{ old('booking_type') == 'all' ? 'selected' : '' }}>Tất cả đơn đặt</option>
                                <option value="single" {{ old('booking_type') == 'single' ? 'selected' : '' }}>Đơn đặt theo buổi</option>
                                <option value="subscription" {{ old('booking_type') == 'subscription' ? 'selected' : '' }}>Đơn đặt định kỳ</option>
                            </select>
                            @error('booking_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('promotions.index') }}" 
                                class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                                Hủy
                            </a>
                            <button type="submit" 
                                class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition">
                                Thêm khuyến mãi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEndDateField() {
            const isPermanentCheckbox = document.getElementById('is_permanent_checkbox').checked;
            const endDateContainer = document.getElementById('end_date_container');
            const endDateInput = document.getElementById('end_date');
            
            if (isPermanentCheckbox) {
                endDateContainer.style.opacity = '0.5';
                endDateInput.disabled = true;
                endDateInput.required = false;
                endDateInput.value = '';
            } else {
                endDateContainer.style.opacity = '1';
                endDateInput.disabled = false;
                endDateInput.required = true;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', toggleEndDateField);
    </script>
@endsection

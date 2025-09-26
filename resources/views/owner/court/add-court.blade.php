@extends('layout.main-owner')

@section('title', 'Thêm sân mới')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">Thêm sân mới</h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('courts.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 ">Tên sân (nên để chữ số)</label>
                            <input type="text" name="name" id="name" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" id="status" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm bg-white"
                                onchange="toggleMaintenanceDates(this.value)">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Sẵn sàng</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="maintenance_dates" class="space-y-4 {{ old('status') == 'maintenance' ? '' : 'hidden' }}">
                            <div>
                                <label for="maintenance_start_date" class="block text-sm font-medium text-gray-700">Ngày bắt đầu bảo trì</label>
                                <input type="datetime-local" name="maintenance_start_date" id="maintenance_start_date"
                                    class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                    value="{{ old('maintenance_start_date') }}">
                                @error('maintenance_start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="maintenance_end_date" class="block text-sm font-medium text-gray-700">Ngày kết thúc bảo trì</label>
                                <input type="datetime-local" name="maintenance_end_date" id="maintenance_end_date"
                                    class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                    value="{{ old('maintenance_end_date') }}">
                                @error('maintenance_end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Hình ảnh</label>
                            <input type="file" name="image" id="image" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                accept="image/*">
                            <p class="mt-1 text-sm text-gray-500">Hình ảnh giúp người dùng dễ hình dung vị trí sân</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('courts.index') }}" 
                                class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                                Hủy
                            </a>
                            <button type="submit" 
                                class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition">
                                Thêm sân
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMaintenanceDates(status) {
            const maintenanceDates = document.getElementById('maintenance_dates');
            if (status === 'maintenance') {
                maintenanceDates.classList.remove('hidden');
            } else {
                maintenanceDates.classList.add('hidden');
            }
        }
    </script>
@endsection

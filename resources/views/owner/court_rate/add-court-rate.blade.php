@extends('layout.main-owner')

@section('title', 'Thêm giá thuê mới')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">Thêm giá thuê mới</h2>
                </div>

                @if($errors->has('time_conflict'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ $errors->first('time_conflict') }}
                    </div>
                @endif

                <div class="p-6">
                    <form action="{{ route('court-rates.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngày trong tuần</label>
                            <div class="grid grid-cols-4 gap-4">
                                @php
                                    use App\Models\CourtRate;
                                    $dayMapping = [2, 3, 4, 5, 6, 7, 8]; // Thứ 2 đến Chủ nhật
                                @endphp
                                
                                @foreach($dayMapping as $dayValue)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $dayValue }}" 
                                            class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                            {{ in_array($dayValue, old('days_of_week', [])) ? 'checked' : '' }}>
                                        <label class="ml-2 text-sm text-gray-700">{{ CourtRate::getDayNameStatic($dayValue) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('days_of_week')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Giờ bắt đầu</label>
                            <input type="time" name="start_time" id="start_time" min="05:00" max="24:00"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">Giờ kết thúc</label>
                            <input type="time" name="end_time" id="end_time" min="05:00" max="24:00"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price_per_hour" class="block text-sm font-medium text-gray-700">Giá/giờ</label>
                            <input type="number" name="price_per_hour" id="price_per_hour"
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('price_per_hour') }}" required min="0">
                            @error('price_per_hour')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('court-rates.index') }}" 
                                class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                                Hủy
                            </a>
                            <button type="submit" 
                                class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition">
                                Thêm giá thuê
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layout.main-customer')

@section('title', 'Chỉnh sửa hồ sơ tìm đồng đội')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Main Content -->
    <div class="w-full bg-white rounded-lg shadow-lg p-6 max-w-3xl mx-auto">
        <div class="mb-6 border-b border-gray-200 pb-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Chỉnh sửa hồ sơ tìm đồng đội</h1>
                    <p class="text-gray-600 mt-2">Cập nhật thông tin để tìm được đồng đội phù hợp</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('teammate.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition duration-200 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Quay lại
                    </a>
                    <button id="delete-profile" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition duration-200 ease-in-out shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Xóa hồ sơ
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Edit Form -->
        <form action="{{ route('teammate.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block mb-2 text-sm font-medium text-gray-700">Họ và tên</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $teammate->full_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('full_name') border-red-500 @enderror">
                    @error('full_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Skill Level -->
                <div>
                    <label for="skill_level" class="block mb-2 text-sm font-medium text-gray-700">Trình độ</label>
                    <select id="skill_level" name="skill_level" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('skill_level') border-red-500 @enderror appearance-none bg-white">
                        <option value="">Chọn trình độ</option>
                        <option value="yếu" {{ old('skill_level', $teammate->skill_level) == 'yếu' ? 'selected' : '' }}>Yếu</option>
                        <option value="trung bình yếu" {{ old('skill_level', $teammate->skill_level) == 'trung bình yếu' ? 'selected' : '' }}>Trung bình yếu</option>
                        <option value="trung bình" {{ old('skill_level', $teammate->skill_level) == 'trung bình' ? 'selected' : '' }}>Trung bình</option>
                        <option value="trung bình khá" {{ old('skill_level', $teammate->skill_level) == 'trung bình khá' ? 'selected' : '' }}>Trung bình khá</option>
                        <option value="khá" {{ old('skill_level', $teammate->skill_level) == 'khá' ? 'selected' : '' }}>Khá</option>
                    </select>
                    @error('skill_level')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Contact Info -->
            <div>
                <label for="contact_info" class="block mb-2 text-sm font-medium text-gray-700">Thông tin liên hệ</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                    </div>
                    <input type="text" id="contact_info" name="contact_info" value="{{ old('contact_info', $teammate->contact_info) }}" required
                        class="w-full pl-10 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('contact_info') border-red-500 @enderror"
                        placeholder="Số điện thoại, email, Facebook, Zalo,...">
                </div>
                @error('contact_info')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Play Time -->
            <div>
                <label for="play_time" class="block mb-2 text-sm font-medium text-gray-700">Khoảng thời gian thường chơi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="play_time" name="play_time" value="{{ old('play_time', $teammate->play_time) }}"
                        class="w-full pl-10 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('play_time') border-red-500 @enderror"
                        placeholder="Ví dụ: Tối thứ 2, 4, 6 từ 18h-20h">
                </div>
                @error('play_time')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Expectations -->
            <div>
                <label for="expectations" class="block mb-2 text-sm font-medium text-gray-700">Mong muốn tìm đồng đội như thế nào?</label>
                <div class="relative">
                    <textarea id="expectations" name="expectations" rows="4"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('expectations') border-red-500 @enderror"
                        placeholder="Mô tả đồng đội bạn đang tìm kiếm...">{{ old('expectations', $teammate->expectations) }}</textarea>
                </div>
                @error('expectations')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 ease-in-out inline-flex items-center shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Cập nhật
                </button>
            </div>
        </form>
        
        <!-- Hidden Delete Form -->
        <form id="delete-form" action="{{ route('teammate.delete') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>
@endsection